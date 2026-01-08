<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin || ! $user->is_active) {
            return redirect()->route('login')->with('error', 'Brak dostępu do panelu administracyjnego.');
        }

        return $next($request);
    }
}
