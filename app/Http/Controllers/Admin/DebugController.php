<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function index()
    {
        $sqlEnabled = (bool) Setting::get('debug.sql', false);
        $filtersEnabled = (bool) Setting::get('filters.snapshot') !== null;

        return view('admin.debug.index', compact('sqlEnabled', 'filtersEnabled'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'sql_debug' => 'nullable|boolean',
            'refresh_filters' => 'nullable|boolean',
            'refresh_locations' => 'nullable|boolean',
        ]);

        $enabled = (bool) ($data['sql_debug'] ?? false);
        Setting::setValue('debug.sql', $enabled ? '1' : '0', 'bool');

        if (! empty($data['refresh_filters'])) {
            app(\App\Services\FilterService::class)->clear();
            app(\App\Services\FilterService::class)->refresh();
        }

        if (! empty($data['refresh_locations'])) {
            app(\App\Services\LocationService::class)->rebuild();
        }

        return back()->with('status', ($enabled ? 'Debug SQL włączony.' : 'Debug SQL wyłączony.') . (empty($data['refresh_filters']) ? '' : ' Filtry odświeżone.'));
    }
}
