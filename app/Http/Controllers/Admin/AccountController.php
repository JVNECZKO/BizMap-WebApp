<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function edit()
    {
        return view('admin.account.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'password' => 'nullable|string|min:8|confirmed',
            'two_factor_enabled' => 'nullable|boolean',
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->two_factor_enabled = $request->boolean('two_factor_enabled');
        if (! $user->two_factor_enabled) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
        }

        $user->save();

        return back()->with('status', 'Ustawienia konta zapisane.');
    }
}
