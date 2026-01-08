<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdblockGate
{
    public function handle(Request $request, Closure $next)
    {
        // omijamy panel admin, endpointy pomocnicze i assets
        $adminPrefix = config('bizmap.admin_prefix', 'admin');
        $path = trim($request->path(), '/');
        if (
            str_starts_with($path, $adminPrefix) ||
            $path === 'ad-ok' ||
            str_starts_with($path, 'storage') ||
            str_starts_with($path, 'vendor') ||
            str_starts_with($path, 'js') ||
            str_starts_with($path, 'css') ||
            str_starts_with($path, 'images') ||
            str_starts_with($path, 'fonts')
        ) {
            return $next($request);
        }

        // jeśli mamy cookie, przepuszczamy
        if ($request->cookie('ad_ok') === '1') {
            return $next($request);
        }

        // dla żądań ajax/JSON zwracamy 403
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Prosimy wyłączyć blokowanie reklam.'], 403);
        }

        return response()->view('adblock.require', [
            'redirect' => $request->fullUrl(),
        ]);
    }
}
