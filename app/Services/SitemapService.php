<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    protected int $urlsPerFile = 2000;
    protected string $jobKey = 'sitemap_job_state';

    public function listFiles(): array
    {
        $files = File::glob(public_path('sitemap-companies-*.xml')) ?: [];
        $files = array_map(fn($path) => basename($path), $files);
        sort($files, SORT_NATURAL);

        return $files;
    }

    public function start(): void
    {
        // wyczyść stare pliki
        foreach (File::glob(public_path('sitemap-companies-*.xml')) as $old) {
            @File::delete($old);
        }
        @File::delete(public_path('sitemap.xml'));

        $state = [
            'last_id' => 0,
            'file_index' => 1,
            'files' => [],
        ];

        Cache::forever($this->jobKey, $state);
    }

    public function runChunk(): array
    {
        $state = Cache::get($this->jobKey);
        if (! $state) {
            return ['status' => 'idle'];
        }

        $chunk = Business::query()
            ->select(['id', 'slug', 'updated_at'])
            ->where('id', '>', $state['last_id'])
            ->orderBy('id')
            ->limit($this->urlsPerFile)
            ->get();

        if ($chunk->isEmpty()) {
            $indexXml = $this->renderIndex($state['files']);
            File::put(public_path('sitemap.xml'), $indexXml);
            Setting::setValue('sitemap.last_generated_at', now()->toDateTimeString());
            Cache::forget($this->jobKey);

            return [
                'status' => 'finished',
                'files' => $state['files'],
            ];
        }

        $fileName = "sitemap-companies-{$state['file_index']}.xml";
        $xml = $this->renderUrlSet($chunk);
        File::put(public_path($fileName), $xml);

        $state['files'][] = $fileName;
        $state['last_id'] = $chunk->last()->id;
        $state['file_index']++;

        Cache::forever($this->jobKey, $state);

        return [
            'status' => 'running',
            'file' => $fileName,
            'last_id' => $state['last_id'],
            'files_count' => count($state['files']),
        ];
    }

    protected function renderUrlSet($businesses): string
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
