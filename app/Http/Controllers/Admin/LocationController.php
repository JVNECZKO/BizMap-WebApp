<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LocationService;

class LocationController extends Controller
{
    public function index()
    {
        return view('admin.locations.index');
    }

    public function rebuild(LocationService $locationService)
    {
        @set_time_limit(0);
        $locationService->rebuild();

        return response()->json(['status' => 'ok']);
    }
}
