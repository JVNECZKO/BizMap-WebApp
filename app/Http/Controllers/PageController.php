<?php

namespace App\Http\Controllers;

use App\Models\PkdCode;
use App\Models\Setting;

class PageController extends Controller
{
    public function pkd()
    {
        $version = Setting::get('pkd.version', '2007');
        $codes = PkdCode::query()
            ->where('version', $version)
            ->orderBy('code')
            ->get();

        return view('pages.pkd', [
            'codes' => $codes,
            'pkdVersion' => $version,
        ]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact', [
            'contactEmail' => Setting::get('contact.email', 'kontakt@dropdigital.pl'),
            'contactPhone' => Setting::get('contact.phone', ''),
            'contactAddress' => Setting::get('contact.address', ''),
            'contactHeadline' => Setting::get('contact.headline', 'Skontaktuj się z nami'),
            'contactIntro' => Setting::get('contact.intro', 'Masz pytania dotyczące danych CEIDG lub importów? Napisz do nas.'),
            'contactSuccess' => session('contact_success'),
        ]);
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}
