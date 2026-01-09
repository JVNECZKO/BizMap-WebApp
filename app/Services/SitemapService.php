<?php

namespace App\Services;

use App\Models\Business;
use App\Models\PkdCode;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SitemapService
{
    protected int $urlsPerFile = 2000;
    protected string $jobKey = 'sitemap_job_state';

    public function listFiles(): array
    {
        $companyFiles = File::glob(public_path('sitemap-companies-*.xml')) ?: [];
        $pkdFiles = File::glob(public_path('sitemap-pkd-*.xml')) ?: [];

        $files = array_merge($companyFiles, $pkdFiles);
        $files = array_map(fn($path) => basename($path), $files);
        sort($files, SORT_NATURAL);

        return $files;
    }

    public function clearAll(): void
    {
        foreach (File::glob(public_path('sitemap-companies-*.xml')) as $old) {
            @File::delete($old);
        }
        foreach (File::glob(public_path('sitemap-pkd-*.xml')) as $old) {
            @File::delete($old);
        }
        @File::delete(public_path('sitemap.xml'));

        Cache::forget($this->jobKey);
        Setting::setValue('sitemap.last_generated_at', null);
    }

    public function start(): void
    {
        // wyczyść stare pliki
        $this->clearAll();

        $pkdVersion = Setting::get('pkd.version', '2007');
        $pkdCodes = PkdCode::query()
            ->where('version', $pkdVersion)
            ->where('is_leaf', true)
            ->orderBy('code')
            ->get(['code', 'name'])
            ->map(fn($item) => [
                'code' => $item->code,
                'slug' => Str::slug($item->code . ' ' . $item->name),
            ])
            ->values()
            ->toArray();

        $regions = app(\App\Services\FilterService::class)->get()['wojewodztwa'] ?? [];
        $regionEntries = collect($regions)
            ->map(fn($w) => ['name' => $w, 'slug' => Str::slug($w)])
            ->values()
            ->toArray();

        $state = [
            'phase' => 'companies',
            'last_id' => 0,
            'file_index' => 1,
            'files' => [],
            'processed' => 0,
            'pkd' => [
                'codes' => $pkdCodes,
                'regions' => $regionEntries,
                'code_index' => 0,
                'region_index' => 0,
            ],
        ];

        Cache::forever($this->jobKey, $state);
    }

    public function runChunk(int $steps = 1): array
    {
        $state = Cache::get($this->jobKey);
        if (! $state) {
            return ['status' => 'idle'];
        }

        $steps = max(1, min($steps, 50));
        $filesDone = 0;
        $lastFile = null;

        while ($filesDone < $steps) {
            if ($state['phase'] === 'companies') {
                $chunk = Business::query()
                    ->select(['id', 'slug', 'updated_at'])
                    ->where('id', '>', $state['last_id'])
                    ->orderBy('id')
                    ->limit($this->urlsPerFile)
                    ->get();

                if ($chunk->isEmpty()) {
                    // przechodzimy do fazy pkd
                    $state['phase'] = 'pkd';
                    $state['last_id'] = 0;
                    continue;
                }

                $fileName = "sitemap-companies-{$state['file_index']}.xml";
                $xml = $this->renderBusinessUrlSet($chunk);
                File::put(public_path($fileName), $xml);
                $lastFile = $fileName;

                $state['files'][] = $fileName;
                $state['last_id'] = $chunk->last()->id;
                $state['file_index']++;
                $state['processed'] += $chunk->count();
                $filesDone++;
                continue;
            }

            // faza pkd + regiony
            $urls = [];
            $codes = $state['pkd']['codes'];
            $regions = array_merge([['name' => null, 'slug' => null]], $state['pkd']['regions']);

            while (count($urls) < $this->urlsPerFile && $state['pkd']['code_index'] < count($codes)) {
                $codeEntry = $codes[$state['pkd']['code_index']];

                for ($i = $state['pkd']['region_index']; $i < count($regions) && count($urls) < $this->urlsPerFile; $i++) {
                    $region = $regions[$i];
                    $params = [
                        'code' => $codeEntry['code'],
                        'slug' => $codeEntry['slug'],
                    ];
                    if (! empty($region['slug'])) {
                        $params['region'] = $region['slug'];
                    }

                    $urls[] = [
                        'loc' => route('seo.pkd', $params),
                        'lastmod' => now(),
                    ];
                }

                if ($i >= count($regions)) {
                    $state['pkd']['code_index']++;
                    $state['pkd']['region_index'] = 0;
                } else {
                    $state['pkd']['region_index'] = $i;
                }
            }

            if (empty($urls)) {
                $indexXml = $this->renderIndex($state['files']);
                File::put(public_path('sitemap.xml'), $indexXml);
                Setting::setValue('sitemap.last_generated_at', now()->toDateTimeString());
                Cache::forget($this->jobKey);

                return [
                    'status' => 'finished',
                    'files' => $state['files'],
                    'total_processed' => $state['processed'],
                ];
            }

            $fileName = "sitemap-pkd-{$state['file_index']}.xml";
            $xml = $this->renderGenericUrlSet($urls);
            File::put(public_path($fileName), $xml);
            $lastFile = $fileName;

            $state['files'][] = $fileName;
            $state['file_index']++;
            $filesDone++;
        }

        Cache::forever($this->jobKey, $state);

        return [
            'status' => 'running',
            'phase' => $state['phase'],
            'file' => $lastFile,
            'files_count' => count($state['files']),
            'processed' => $state['processed'],
            'pkd_progress' => [
                'codes_done' => $state['pkd']['code_index'],
                'total_codes' => count($state['pkd']['codes']),
            ],
        ];
    }

    protected function renderBusinessUrlSet($businesses): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        $xml[] = $this->urlTag(URL::to('/'), now());
        $xml[] = $this->urlTag(URL::to('/firmy'), now());

        foreach ($businesses as $business) {
            $loc = URL::to('/firma/' . $business->id . '-' . $business->slug);
            $xml[] = $this->urlTag($loc, $business->updated_at ?? now());
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }

    protected function renderGenericUrlSet(array $urls): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $entry) {
            $xml[] = $this->urlTag($entry['loc'], $entry['lastmod'] ?? now());
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }

    protected function renderIndex(array $files): string
    {
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($files as $file) {
            $xml[] = '  <sitemap>';
            $xml[] = '    <loc>' . e(URL::to('/' . $file)) . '</loc>';
            $xml[] = '    <lastmod>' . now()->toAtomString() . '</lastmod>';
            $xml[] = '  </sitemap>';
        }

        $xml[] = '</sitemapindex>';

        return implode("\n", $xml);
    }

    protected function urlTag(string $loc, $lastMod): string
    {
        return sprintf(
            '  <url><loc>%s</loc><lastmod>%s</lastmod><changefreq>weekly</changefreq></url>',
            e($loc),
            $lastMod instanceof \DateTimeInterface ? $lastMod->toAtomString() : now()->toAtomString()
        );
    }
}
