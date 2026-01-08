<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Services\BusinessImportService;
use App\Services\BusinessSearchService;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'status']);
        $businesses = Business::query()
            ->filter($filters)
            ->recent()
            ->cursorPaginate(25);

        return view('admin.businesses.index', compact('businesses', 'filters'));
    }

    public function edit(Business $business)
    {
        return view('admin.businesses.edit', compact('business'));
    }

    public function update(Request $request, Business $business, BusinessSearchService $searchService)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:191',
            'nip' => 'nullable|string|max:32',
            'regon' => 'nullable|string|max:32',
            'status_dzialalnosci' => 'nullable|string|max:100',
            'kod_pocztowy' => 'nullable|string|max:20',
            'powiat' => 'nullable|string|max:150',
            'gmina' => 'nullable|string|max:150',
            'miejscowosc' => 'nullable|string|max:150',
            'glowny_kod_pkd' => 'nullable|string|max:10',
        ]);

        $business->fill($data);
        $business->save();
        $searchService->clear();

        return back()->with('status', 'Zapisano zmiany.');
    }

    public function destroy(Business $business, BusinessSearchService $searchService)
    {
        $business->delete();
        $searchService->clear();

        return back()->with('status', 'Wpis został usunięty.');
    }

    public function bulkDestroy(Request $request, BusinessSearchService $searchService)
    {
        $ids = $request->input('ids', []);

        if (! empty($ids)) {
            Business::whereIn('id', $ids)->delete();
            $searchService->clear();
        }

        return back()->with('status', 'Usunięto zaznaczone wpisy.');
    }

    public function wipeAll(BusinessImportService $importService, BusinessSearchService $searchService)
    {
        $importService->wipeBusinesses();
        $searchService->clear();
        cache()->flush();

        return back()->with('status', 'Wszystkie wpisy zostały usunięte, cache wyczyszczony.');
    }
}
