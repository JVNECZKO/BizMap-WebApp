<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

class SitemapService
{
    protected int $urlsPerFile = 2000;

    public function generate(): array
    {
        $files = [];
        $counter = 1;

        Business::query()
            ->select(['id', 'slug', 'updated_at'])
            ->orderBy('id')
            ->chunk($this->urlsPerFile, function ($businesses) use (&$files, &$counter) {
                $fileName = "sitemap-companies-{$counter}.xml";
                $filePath = public_path($fileName);

                $xml = $this->renderUrlSet($businesses);
                File::put($filePath, $xml);
                $files[] = $fileName;
                $counter++;
            });

        $indexXml = $this->renderIndex($files);
        File::put(public_path('sitemap.xml'), $indexXml);

        Setting::setValue('sitemap.last_generated_at', now()->toDateTimeString());

        return $files;
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
