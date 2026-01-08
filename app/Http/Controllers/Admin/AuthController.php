<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Nieprawidłowy login lub hasło.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_admin || ! $user->is_active) {
            Auth::logout();
            return back()->withErrors(['email' => 'Brak uprawnień administracyjnych.']);
        }

        if ($user->two_factor_enabled) {
            $code = random_int(100000, 999999);
            $user->two_factor_code = Hash::make($code);
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                Mail::raw("Twój kod logowania BizMap: {$code}", function ($msg) use ($user) {
                    $msg->to($user->email)->subject('Kod logowania BizMap');
                });
            } catch (\Throwable $e) {
                // ignorujemy błąd wysyłki
            }

            $request->session()->put('2fa:user_id', $user->id);
            $request->session()->put('2fa:remember', $request->boolean('remember'));

            Auth::logout();

            return redirect()->route('admin.2fa.show')->with('status', 'Wpisz kod wysłany na e-mail.');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard'));

    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
