<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function index()
    {
        $providers = Setting::get('ads.providers', null);
        // tylko gdy brak jakiejkolwiek konfiguracji (pierwsze uruchomienie) ładujemy domyślną listę
        if ($providers === null) {
            $providers = $this->defaultProviders();
            Setting::setValue('ads.providers', $providers, 'json');
        } elseif (! is_array($providers)) {
            $providers = [];
        }

        return view('admin.ads.index', compact('providers'));
    }

    public function update(Request $request)
    {
        $names = $request->input('names', []);
        $codes = $request->input('codes', []);
        $enabled = $request->input('enabled', []);

        $providers = [];
        foreach ($names as $idx => $name) {
            $name = trim($name);
            $code = $codes[$idx] ?? '';
            if ($name === '' && trim($code) === '') {
                continue;
            }
            $providers[] = [
                'name' => $name ?: 'Reklamodawca',
                'code' => $code,
                'enabled' => in_array((string)$idx, $enabled, true),
            ];
        }

        Setting::setValue('ads.providers', $providers, 'json');

        return redirect()->route('admin.ads.index')->with('status', 'Zapisano konfigurację reklam.');
    }

    protected function defaultProviders(): array
    {
        return [
            [
                'name' => 'Google AdSense',
                'enabled' => true,
                'code' => '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6310815970812781" crossorigin="anonymous"></script>
<script>(adsbygoogle=window.adsbygoogle||[]).push({google_ad_client:"ca-pub-6310815970812781", enable_page_level_ads:true});</script>',
            ],
        ];
    }
}
