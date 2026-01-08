<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DebugSql
{
    public function handle(Request $request, Closure $next)
    {
        $enabled = Setting::get('debug.sql', false);

        if (! $enabled || ! Auth::check() || ! Auth::user()->is_admin) {
            return $next($request);
        }

        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $sql = $this->interpolateQuery($query->sql, $query->bindings);
            $queries[] = [
                'sql' => $sql,
                'time' => $query->time,
            ];
        });

        $response = $next($request);

        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type');
        if (! $contentType || ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $total = array_sum(array_column($queries, 'time'));
        $debugHtml = view('partials.sql-debug', [
            'queries' => $queries,
            'total' => $total,
        ])->render();

        $response->setContent($response->getContent() . $debugHtml);

        return $response;
    }

    protected function interpolateQuery(string $sql, array $bindings): string
    {
        $pdo = DB::getPdo();
        foreach ($bindings as $binding) {
            $value = is_numeric($binding) ? $binding : $pdo->quote((string) $binding);
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }

        return $sql;
    }
}
