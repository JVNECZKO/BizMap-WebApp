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

    public function run(SitemapService $sitemapService)
    {
        $result = $sitemapService->runChunk();

        return response()->json($result);
    }
}
