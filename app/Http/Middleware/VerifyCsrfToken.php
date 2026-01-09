<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '*/baza/migration/start',
        '*/baza/migration/run',
        '*/baza/migration/save',
        '*/baza/migration/clear',
        'admin-x7k9p3/baza/migration/start',
        'admin-x7k9p3/baza/migration/run',
        'admin-x7k9p3/baza/migration/save',
        'admin-x7k9p3/baza/migration/clear',
    ];
}
