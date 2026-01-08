<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SitemapService;

class SitemapController extends Controller
{
    public function index()
    {
        return view('admin.sitemap.index', [
            'lastGenerated' => Setting::get('sitemap.last_generated_at'),
        ]);
    }

    public function generate(SitemapService $sitemapService)
    {
        $files = $sitemapService->generate();

        return back()->with('status', 'Wygenerowano sitemapę (' . count($files) . ' plików).');
    }
}
