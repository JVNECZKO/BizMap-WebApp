<?php

namespace App\Http\Controllers;

use App\Models\PkdCode;
use App\Models\Setting;
use App\Services\BusinessSearchService;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    public function index(BusinessSearchService $searchService)
    {
        $latest = $searchService->latest(12);
        $popular = $searchService->popularPkd(8);
        $pkdVersion = Setting::get('pkd.version', '2007');
        $pkdNames = Cache::rememberForever("pkd_names_{$pkdVersion}", function () use ($pkdVersion) {
            return PkdCode::query()
                ->where('version', $pkdVersion)
                ->get()
                ->keyBy('code');
        });

        return view('landing', [
            'latest' => $latest,
            'popular' => $popular,
            'pkdNames' => $pkdNames,
            'metaTitle' => Setting::get('seo.meta_title', config('app.name')),
            'metaDescription' => Setting::get('seo.meta_description', ''),
            'metaKeywords' => Setting::get('seo.meta_keywords', ''),
        ]);
    }
}
