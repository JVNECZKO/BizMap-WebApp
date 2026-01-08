@extends('layouts.admin')

@section('content')
<div class="max-w-4xl space-y-4">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Lokalizacje</p>
        <h1 class="text-2xl font-bold text-slate-900">Powiązania lokalizacji</h1>
        <p class="text-slate-600">Przelicz drzewo województwo → powiat → gmina → miejscowość i zapisz w cache. Użyj po dużych aktualizacjach danych.</p>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 shadow-panel p-6 space-y-4">
        <div class="flex items-center gap-3">
            <button id="rebuild-btn" class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel hover:bg-slate-800">Odbuduj powiązania</button>
            <span id="rebuild-status" class="text-sm text-slate-600">Gotowe</span>
        </div>
        <p class="text-xs text-slate-500">Operacja może potrwać w zależności od wielkości bazy. Trwa w tle na tym żądaniu AJAX.</p>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('rebuild-btn').addEventListener('click', async () => {
        const status = document.getElementById('rebuild-status');
        status.innerText = 'Przeliczanie...';
        const res = await fetch('{{ route('admin.locations.rebuild') }}', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        });
        if (res.ok) {
            status.innerText = 'Zakończono.';
        } else {
            status.innerText = 'Błąd podczas przeliczania.';
        }
    });
</script>
@endpush
@endsection
