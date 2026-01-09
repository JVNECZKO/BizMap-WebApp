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
        '*/baza/migration/direct',
        '*/baza/transfer/start',
        '*/baza/transfer/run',
        '*/baza/transfer/save',
        '*/baza/transfer/clear',
        '*/baza/transfer/direct',
        'admin-x7k9p3/baza/migration/start',
        'admin-x7k9p3/baza/migration/run',
        'admin-x7k9p3/baza/migration/save',
        'admin-x7k9p3/baza/migration/clear',
        'admin-x7k9p3/baza/migration/direct',
        'admin-x7k9p3/baza/transfer/start',
        'admin-x7k9p3/baza/transfer/run',
        'admin-x7k9p3/baza/transfer/save',
        'admin-x7k9p3/baza/transfer/clear',
        'admin-x7k9p3/baza/transfer/direct',
    ];
}
