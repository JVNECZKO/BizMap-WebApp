<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SitemapService;

class SitemapController extends Controller
{
    public function index(SitemapService $sitemapService)
    {
        return view('admin.sitemap.index', [
            'lastGenerated' => Setting::get('sitemap.last_generated_at'),
            'files' => $sitemapService->listFiles(),
        ]);
    }

    public function start(SitemapService $sitemapService)
    {
        $sitemapService->start();

        return response()->json(['status' => 'started']);
    }

    public function update(SitemapService $sitemapService)
    {
        $sitemapService->startUpdate();

        return response()->json(['status' => 'started']);
    }

    public function startPkd(SitemapService $sitemapService)
    {
        $sitemapService->startPkdOnly();

        return response()->json(['status' => 'started']);
    }

    public function run(SitemapService $sitemapService)
    {
        $steps = (int) request()->input('steps', 10);
        $steps = max(1, min($steps, 50)); // safety

        $result = $sitemapService->runChunk($steps);

        return response()->json($result);
    }

    public function clear(SitemapService $sitemapService)
    {
        $sitemapService->clearAll();

        return response()->json(['status' => 'cleared']);
    }

    public function reindex(SitemapService $sitemapService)
    {
        $result = $sitemapService->rebuildIndexOnly();

        return response()->json($result);
    }
}
