<?php

namespace App\Http\Controllers;

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

        return view('companies.show', [
            'business' => $business,
            'pkdNames' => $pkdNames,
            'pkdVersion' => $pkdVersion,
        ]);
    }
}
