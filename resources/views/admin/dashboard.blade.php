@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Pulpit</p>
        <h1 class="text-3xl font-bold text-slate-900">Szybki przegląd</h1>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.imports.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white shadow-panel">Nowy import</a>
        <a href="{{ route('admin.businesses.index') }}" class="px-4 py-2 rounded-xl border border-slate-200">Lista firm</a>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-5 mb-8">
    <div class="p-5 rounded-2xl bg-white shadow-panel border border-slate-100">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Firmy</p>
        <p class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($stats['businesses']) }}</p>
        <p class="text-sm text-slate-500">w bazie</p>
    </div>
    <div class="p-5 rounded-2xl bg-white shadow-panel border border-slate-100">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD</p>
        <p class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($stats['pkdCodes']) }}</p>
        <p class="text-sm text-slate-500">kodów w systemie</p>
    </div>
    <div class="p-5 rounded-2xl bg-white shadow-panel border border-slate-100">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Import</p>
        @if($stats['lastImport'])
            <p class="text-sm text-slate-700 mt-2">Ostatni plik: <span class="font-semibold">{{ $stats['lastImport']->filename }}</span></p>
            <p class="text-sm text-slate-500">Status: {{ $stats['lastImport']->status }}</p>
            <p class="text-sm text-slate-500">Zaimportowano: {{ number_format($stats['lastImport']->imported_rows) }} / {{ number_format($stats['lastImport']->total_rows) }}</p>
        @else
            <p class="text-sm text-slate-500 mt-2">Brak historii importów.</p>
        @endif
    </div>
</div>

<div class="grid md:grid-cols-2 gap-5">
    <div class="p-6 bg-white shadow-panel rounded-2xl border border-slate-100">
        <p class="text-sm font-semibold text-slate-800 mb-3">Checklisty operacyjne</p>
        <ul class="space-y-2 text-sm text-slate-600">
            <li>• Sprawdź ustawienia bazy i połączenie (zakładka Baza danych).</li>
            <li>• Ustal wersję PKD (SEO i nawigacja) – dostępne 2007 i 2025.</li>
            <li>• Przygotuj szablon mapowania kolumn importu, zapisz i przetestuj na próbce.</li>
            <li>• Po imporcie wygeneruj sitemapę (max 2000 URL w pliku).</li>
        </ul>
    </div>
    <div class="p-6 bg-white shadow-panel rounded-2xl border border-slate-100">
        <p class="text-sm font-semibold text-slate-800 mb-3">Bezpieczeństwo</p>
        <ul class="space-y-2 text-sm text-slate-600">
            <li>• Logowanie wyłącznie dla roli admin, użytkownik musi być aktywny.</li>
            <li>• Dane surowe zapisane w osobnej tabeli <code>business_raw_payloads</code>.</li>
            <li>• Czerwony przycisk w sekcji Firmy usuwa wszystkie wpisy i czyści cache.</li>
        </ul>
    </div>
</div>
@endsection
