<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class BrandingController extends Controller
{
    public function index()
    {
        return view('admin.branding.index', [
            'logo' => Setting::get('branding.logo'),
            'favicon' => Setting::get('branding.favicon'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|mimes:png,jpg,jpeg,ico,svg,webp|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            Setting::setValue('branding.logo', $path, 'string');
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            Setting::setValue('branding.favicon', $path, 'string');
        }

        return back()->with('status', 'Zapisano branding.');
    }
}
