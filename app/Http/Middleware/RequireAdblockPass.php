<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAdblockPass
{
    public function handle(Request $request, Closure $next)
    {
        // allow the check endpoints always
        if ($request->is('ab/*')) {
            return $next($request);
        }

        // If not passed, show only the gate shell (no content)
        if (!$request->cookies->has('ab_ok')) {
            return response()->view('ab.gate', [
                'next' => $request->fullUrl(),
            ], 403);
        }

        return $next($request);
    }
}
