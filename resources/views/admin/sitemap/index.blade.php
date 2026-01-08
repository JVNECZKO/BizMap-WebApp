@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Sitemap</p>
        <h1 class="text-2xl font-bold text-slate-900">Generator mapy stron</h1>
        <p class="text-sm text-slate-600">Każdy plik maksymalnie 2000 adresów URL.</p>
    </div>
</div>

<div class="bg-white shadow-panel rounded-2xl border border-slate-100 p-6 space-y-4">
    <p class="text-sm text-slate-700">Ostatnie generowanie: {{ $lastGenerated ?? 'brak danych' }}</p>
    <form method="POST" action="{{ route('admin.sitemap.generate') }}">
        @csrf
        <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Generuj sitemapę</button>
    </form>
</div>
@endsection
