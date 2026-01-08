<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('admin.auth.twofactor');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|min:4|max:10',
        ]);

        $userId = $request->session()->pull('2fa:user_id');
        $remember = $request->session()->pull('2fa:remember', false);

        if (! $userId) {
            return redirect()->route('login')->withErrors(['email' => 'Sesja logowania wygasła.']);
        }

        $user = User::find($userId);
        if (! $user || ! $user->two_factor_enabled || ! $user->two_factor_code || ! $user->two_factor_expires_at) {
            return redirect()->route('login')->withErrors(['email' => 'Kod nieważny.']);
        }

        if ($user->two_factor_expires_at->isPast()) {
            return redirect()->route('login')->withErrors(['email' => 'Kod wygasł.']);
        }

        if (! Hash::check($data['code'], $user->two_factor_code)) {
            return back()->withErrors(['code' => 'Nieprawidłowy kod.']);
        }

        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}
