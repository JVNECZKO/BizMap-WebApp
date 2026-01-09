<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PkdCode;
use App\Models\Setting;
use App\Services\BusinessSearchService;

class CompanyController extends Controller
{
    public function show(int $id, string $slug, BusinessSearchService $searchService)
    {
        $business = $searchService->company($id);

        if (! $business) {
            abort(404);
        }

        if ($business->slug !== $slug) {
            return redirect()->route('company.show', ['id' => $business->id, 'slug' => $business->slug], 301);
        }

        $pkdVersion = Setting::get('pkd.version', '2007');
        $pkdNames = PkdCode::query()
            ->where('version', $pkdVersion)
            ->whereIn('code', $business->pkdCodes->pluck('pkd_code'))
            ->get()
            ->keyBy('code');

        $samePkd = collect();
        if ($business->glowny_kod_pkd) {
            $samePkd = Business::query()
                ->select(['id', 'full_name', 'slug', 'miejscowosc', 'powiat', 'wojewodztwo', 'glowny_kod_pkd'])
                ->where('glowny_kod_pkd', $business->glowny_kod_pkd)
                ->where('id', '!=', $business->id)
                ->orderByDesc('imported_at')
                ->limit(8)
                ->get();
        }

        $sameCity = collect();
        if ($business->miejscowosc) {
            $sameCity = Business::query()
                ->select(['id', 'full_name', 'slug', 'miejscowosc', 'powiat', 'wojewodztwo', 'glowny_kod_pkd'])
                ->where('miejscowosc', $business->miejscowosc)
                ->where('id', '!=', $business->id)
                ->orderByDesc('imported_at')
                ->limit(8)
                ->get();
        }

        $sameRegion = collect();
        if ($business->wojewodztwo) {
            $sameRegion = Business::query()
                ->select(['id', 'full_name', 'slug', 'miejscowosc', 'powiat', 'wojewodztwo', 'glowny_kod_pkd'])
                ->where('wojewodztwo', $business->wojewodztwo)
                ->where('id', '!=', $business->id)
                ->orderByDesc('imported_at')
                ->limit(8)
                ->get();
        }

        return view('companies.show', [
            'business' => $business,
            'pkdNames' => $pkdNames,
            'pkdVersion' => $pkdVersion,
            'samePkd' => $samePkd,
            'sameCity' => $sameCity,
            'sameRegion' => $sameRegion,
        ]);
    }
}
