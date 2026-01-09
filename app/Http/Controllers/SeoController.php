<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PkdCode;
use App\Models\Setting;
use App\Services\FilterService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SeoController extends Controller
{
    public function pkdLanding(string $code, string $slug, ?string $region = null)
    {
        $pkdVersion = Setting::get('pkd.version', '2007');
        $pkdCodesList = PkdCode::query()
            ->where('version', $pkdVersion)
            ->where('is_leaf', true)
            ->orderBy('code')
            ->get(['code', 'name']);

        $pkd = $pkdCodesList->firstWhere('code', $code);

        if (! $pkd) {
            abort(404);
        }

        $expectedSlug = Str::slug($pkd->code . ' ' . $pkd->name);
        if ($slug !== $expectedSlug) {
            return redirect()->route('seo.pkd', ['code' => $pkd->code, 'slug' => $expectedSlug, 'region' => $region], 301);
        }

        $filterService = app(FilterService::class);
        $filters = $filterService->get();

        $regionName = null;
        $regionSlug = null;
        if ($region) {
            foreach ($filters['wojewodztwa'] as $w) {
                if (Str::slug($w) === $region) {
                    $regionName = $w;
                    $regionSlug = $region;
                    break;
                }
            }

            if (! $regionName) {
                abort(404);
            }
        }

        $companiesQuery = Business::query()
            ->select(['id', 'full_name', 'slug', 'wojewodztwo', 'powiat', 'miejscowosc', 'glowny_kod_pkd', 'status_dzialalnosci', 'imported_at'])
            ->where('glowny_kod_pkd', $pkd->code);

        if ($regionName) {
            $companiesQuery->where('wojewodztwo', $regionName);
        }

        $companies = $companiesQuery
            ->orderByDesc('imported_at')
            ->limit(30)
            ->get();

        $otherRegions = collect($filters['wojewodztwa'])
            ->map(fn($w) => ['name' => $w, 'slug' => Str::slug($w)])
            ->take(12);

        $relatedCodes = $pkdCodesList
            ->where('code', '!=', $pkd->code)
            ->take(10)
            ->map(function ($item) {
                return [
                    'code' => $item->code,
                    'name' => $item->name,
                    'slug' => Str::slug($item->code . ' ' . $item->name),
                ];
            });

        $allCodes = $pkdCodesList->map(function ($item) {
            return [
                'code' => $item->code,
                'name' => $item->name,
                'slug' => Str::slug($item->code . ' ' . $item->name),
            ];
        })->values();

        $metaTitle = "Firmy PKD {$pkd->code} – {$pkd->name}" . ($regionName ? ' w ' . $regionName : '');
        $metaDescription = "Przegląd firm o kodzie PKD {$pkd->code} ({$pkd->name})" . ($regionName ? " działających w regionie {$regionName}." : '.');

        return view('seo.pkd', [
            'pkd' => $pkd,
            'regionName' => $regionName,
            'regionSlug' => $regionSlug,
            'companies' => $companies,
            'otherRegions' => $otherRegions,
            'relatedCodes' => $relatedCodes,
            'allCodes' => $allCodes,
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
        ]);
    }
}
