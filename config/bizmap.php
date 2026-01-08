<?php

return [
    'admin_prefix' => env('ADMIN_PREFIX', 'admin-x7k9p3'),

    'pagination' => [
        'per_page' => env('BIZMAP_PER_PAGE', 25),
        'cursor_window' => env('BIZMAP_CURSOR_WINDOW', 25),
    ],

    'import' => [
        'chunk' => env('BIZMAP_IMPORT_CHUNK', 2000),
        'preview_rows' => env('BIZMAP_IMPORT_PREVIEW_ROWS', 15),
        'allowed_formats' => ['csv', 'json', 'xml', 'xls', 'xlsx'],
    ],

    'cache' => [
        'enabled' => env('BIZMAP_CACHE', true),
        'ttl' => env('BIZMAP_CACHE_TTL', 900),
    ],

    'contact' => [
        'recipient' => env('BIZMAP_CONTACT_EMAIL', 'kontakt@dropdigital.pl'),
    ],
];
