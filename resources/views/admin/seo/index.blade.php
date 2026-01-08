@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">SEO</p>
        <h1 class="text-2xl font-bold text-slate-900">Meta dane i nawigacja</h1>
    </div>
</div>

<form method="POST" action="{{ route('admin.seo.update') }}" class="bg-white shadow-panel rounded-2xl border border-slate-100 p-6 space-y-4">
    @csrf
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Meta title</label>
        <input type="text" name="meta_title" value="{{ old('meta_title', $title) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Meta description</label>
        <textarea name="meta_description" rows="2" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">{{ old('meta_description', $description) }}</textarea>
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Meta keywords</label>
        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $keywords) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">URL witryny</label>
        <input type="url" name="site_url" value="{{ old('site_url', $siteUrl) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3" placeholder="https://twojadomena.pl">
        <p class="text-xs text-slate-500 mt-1">Adres domeny, pod którą działa serwis.</p>
    </div>
    <div class="grid md:grid-cols-2 gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Wersja PKD</p>
            <div class="mt-2 flex items-center gap-4 text-sm">
                <label class="flex items-center gap-2"><input type="radio" name="pkd_version" value="2007" @checked($pkdVersion === '2007')>2007</label>
                <label class="flex items-center gap-2"><input type="radio" name="pkd_version" value="2025" @checked($pkdVersion === '2025')>2025</label>
            </div>
        </div>
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Prefix panelu admin</p>
            <input type="text" name="admin_prefix" value="{{ old('admin_prefix', $adminPrefix) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
            <p class="text-xs text-slate-500 mt-1">Przykład: admin-x7k9p3</p>
        </div>
    </div>
    <div class="pt-2">
        <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Zapisz</button>
    </div>
</form>
@endsection
