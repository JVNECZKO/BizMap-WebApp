<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdblockController extends Controller
{
    public function allow(Request $request)
    {
        return response()->json(['status' => 'ok'])
            ->cookie('ad_ok', '1', 1440, '/'); // 1 dzień
    }
}
