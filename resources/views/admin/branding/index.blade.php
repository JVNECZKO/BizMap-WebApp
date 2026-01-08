@extends('layouts.admin')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Branding</p>
        <h1 class="text-2xl font-bold text-slate-900">Logo i favicon</h1>
        <p class="text-sm text-slate-600">Wgraj własne logo (nagłówek) oraz faviconę. Pliki zapisywane są w storage/public/branding.</p>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-panel p-6 space-y-6">
        <form method="POST" action="{{ route('admin.branding.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label class="text-sm font-semibold text-slate-800">Logo (PNG/JPG/SVG)</label>
                <input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp" class="mt-2 block w-full text-sm">
                @if($logo)
                    <p class="mt-2 text-xs text-slate-500">Aktualne:</p>
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="mt-1 h-12 rounded">
                @endif
            </div>

            <div>
                <label class="text-sm font-semibold text-slate-800">Favicon (PNG/ICO/SVG)</label>
                <input type="file" name="favicon" accept=".png,.jpg,.jpeg,.ico,.svg,.webp" class="mt-2 block w-full text-sm">
                @if($favicon)
                    <p class="mt-2 text-xs text-slate-500">Aktualna:</p>
                    <img src="{{ asset('storage/' . $favicon) }}" alt="Favicon" class="mt-1 h-8 w-8 rounded">
                @endif
            </div>

            <div>
                <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel hover:bg-slate-800">Zapisz</button>
            </div>
        </form>
    </div>
</div>
@endsection
