<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\PkdCode;
use App\Models\Setting;
use App\Services\BusinessSearchService;
use Illuminate\Support\Str;

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

        $pkdSlug = null;
        if ($business->glowny_kod_pkd && $pkdNames->has($business->glowny_kod_pkd)) {
            $pkdSlug = Str::slug($business->glowny_kod_pkd . ' ' . $pkdNames[$business->glowny_kod_pkd]->name);
        }

        $hotBusinesses = Business::query()
            ->select(['id', 'full_name', 'slug', 'miejscowosc', 'wojewodztwo', 'glowny_kod_pkd'])
            ->where('imported_at', '>=', now()->subDays(7))
            ->orderByDesc('imported_at')
            ->limit(6)
            ->get();

        if ($hotBusinesses->isEmpty()) {
            $hotBusinesses = Business::query()
                ->select(['id', 'full_name', 'slug', 'miejscowosc', 'wojewodztwo', 'glowny_kod_pkd'])
                ->orderByDesc('imported_at')
                ->limit(6)
                ->get();
        }

        return view('companies.show', [
            'business' => $business,
            'pkdNames' => $pkdNames,
            'pkdVersion' => $pkdVersion,
            'samePkd' => $samePkd,
            'sameCity' => $sameCity,
            'sameRegion' => $sameRegion,
            'pkdSlug' => $pkdSlug,
            'hotBusinesses' => $hotBusinesses,
        ]);
    }
}
