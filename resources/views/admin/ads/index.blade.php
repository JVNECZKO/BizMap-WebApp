@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Reklamy</p>
        <h1 class="text-2xl font-bold text-slate-900">Konfiguracja reklamodawców</h1>
        <p class="text-sm text-slate-600 mt-1">Dodaj, wklej kod JS i włącz/wyłącz reklamodawców. Kody są wstrzykiwane do &lt;head&gt; na stronach publicznych.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.ads.update') }}" class="space-y-4">
    @csrf
    <div id="ads-list" class="space-y-4">
        @foreach($providers as $idx => $provider)
            <div class="p-4 bg-white rounded-xl shadow-panel border border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-sm font-semibold text-slate-800">Nazwa</label>
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="enabled[]" value="{{ $idx }}" @checked($provider['enabled'] ?? false) class="rounded border-slate-300">
                        Włączony
                    </label>
                </div>
                <input type="text" name="names[]" value="{{ $provider['name'] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm mb-3" placeholder="Nazwa reklamodawcy">
                <label class="text-sm font-semibold text-slate-800">Kod (JS/HTML)</label>
                <textarea name="codes[]" rows="4" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="<script>...">{{ $provider['code'] ?? '' }}</textarea>
            </div>
        @endforeach
    </div>

    <button type="button" onclick="addProvider()" class="px-4 py-2 rounded-xl border border-slate-300 text-sm text-slate-700">Dodaj reklamodawcę</button>
    <div>
        <button type="submit" class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Zapisz</button>
    </div>
</form>

@push('scripts')
<script>
function addProvider() {
    const idx = document.querySelectorAll('#ads-list > div').length;
    const tpl = `
    <div class="p-4 bg-white rounded-xl shadow-panel border border-slate-100">
        <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-semibold text-slate-800">Nazwa</label>
            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="enabled[]" value="${idx}" class="rounded border-slate-300">
                Włączony
            </label>
        </div>
        <input type="text" name="names[]" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm mb-3" placeholder="Nazwa reklamodawcy">
        <label class="text-sm font-semibold text-slate-800">Kod (JS/HTML)</label>
        <textarea name="codes[]" rows="4" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" placeholder="<script>..."></textarea>
    </div>`;
    document.getElementById('ads-list').insertAdjacentHTML('beforeend', tpl);
}
</script>
@endpush
@endsection
