<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactSettingsController extends Controller
{
    public function index()
    {
        return view('admin.contact.index', [
            'email' => Setting::get('contact.email', 'kontakt@dropdigital.pl'),
            'phone' => Setting::get('contact.phone', ''),
            'address' => Setting::get('contact.address', ''),
            'headline' => Setting::get('contact.headline', 'Skontaktuj się z nami'),
            'intro' => Setting::get('contact.intro', 'Masz pytania dotyczące danych CEIDG lub importów? Napisz do nas.'),
            'success' => Setting::get('contact.success', 'Dziękujemy! Wiadomość została wysłana.'),
            'messages' => ContactMessage::query()->latest()->paginate(15),
        ]);
    }

    public function show(ContactMessage $message)
    {
        return view('admin.contact.show', [
            'message' => $message,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:190',
            'phone' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'headline' => 'nullable|string|max:255',
            'intro' => 'nullable|string|max:500',
            'success' => 'nullable|string|max:255',
        ]);

        Setting::setValue('contact.email', $data['email']);
        Setting::setValue('contact.phone', $data['phone'] ?? '', 'string');
        Setting::setValue('contact.address', $data['address'] ?? '', 'string');
        Setting::setValue('contact.headline', $data['headline'] ?? '', 'string');
        Setting::setValue('contact.intro', $data['intro'] ?? '', 'string');
        Setting::setValue('contact.success', $data['success'] ?? '', 'string');

        return back()->with('status', 'Zapisano dane kontaktowe.');
    }
}
