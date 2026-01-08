<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\ImportLog;
use App\Models\PkdCode;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'businesses' => Business::count(),
            'pkdCodes' => PkdCode::count(),
            'lastImport' => ImportLog::query()->orderByDesc('started_at')->first(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
