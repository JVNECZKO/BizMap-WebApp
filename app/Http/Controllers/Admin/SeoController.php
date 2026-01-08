<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        return view('admin.seo.index', [
            'title' => Setting::get('seo.meta_title', config('app.name')),
            'description' => Setting::get('seo.meta_description'),
            'keywords' => Setting::get('seo.meta_keywords'),
            'pkdVersion' => Setting::get('pkd.version', '2007'),
            'adminPrefix' => Setting::get('admin.prefix', config('bizmap.admin_prefix')),
            'siteUrl' => Setting::get('site.url', url('/')),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'pkd_version' => 'required|in:2007,2025',
            'admin_prefix' => 'required|string|max:50',
            'site_url' => 'nullable|url',
        ]);

        Setting::setValue('seo.meta_title', $data['meta_title']);
        Setting::setValue('seo.meta_description', $data['meta_description'] ?? '');
        Setting::setValue('seo.meta_keywords', $data['meta_keywords'] ?? '');
        Setting::setValue('pkd.version', $data['pkd_version']);
        Setting::setValue('admin.prefix', $data['admin_prefix']);
        if (!empty($data['site_url'])) {
            Setting::setValue('site.url', $data['site_url']);
        }

        return back()->with('status', 'Ustawienia SEO zapisane.');
    }
}
