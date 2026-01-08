<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactFormController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:190',
            'message' => 'required|string|max:4000',
        ]);

        ContactMessage::create($data);

        $recipient = Setting::get('contact.email', config('mail.from.address'));
        $subject = 'Wiadomość z formularza BizMap';

        try {
            Mail::raw(
                "Nadawca: {$data['name']} <{$data['email']}>\n\nTreść:\n{$data['message']}",
                function ($msg) use ($recipient, $subject) {
                    $msg->to($recipient)->subject($subject);
                }
            );
        } catch (\Throwable $e) {
            // wiadomość została zapisana; ciche pominięcie błędu wysyłki e-mail
        }

        return back()->with('contact_success', Setting::get('contact.success', 'Dziękujemy! Wiadomość została wysłana.'));
    }
}
