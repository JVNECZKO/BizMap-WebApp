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
    <div class="space-y-3">
        <button id="start-sitemap" class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Generuj sitemapę (AJAX)</button>
        <button id="clear-sitemap" class="px-5 py-3 rounded-xl bg-white text-slate-800 border border-slate-200">Usuń wszystkie sitemap</button>
        <div id="sitemap-status" class="text-sm text-slate-600">Oczekiwanie...</div>
    </div>
    @if(!empty($files))
        <div class="pt-4">
            <p class="text-sm font-semibold text-slate-800 mb-2">Dostępne pliki sitemap</p>
            <ul class="text-sm text-slate-700 list-disc list-inside space-y-1">
                <li><a class="text-sky-700 hover:text-sky-900 font-semibold" href="/sitemap.xml" target="_blank">sitemap.xml (index)</a></li>
                @foreach($files as $file)
                    <li><a class="text-sky-700 hover:text-sky-900" href="/{{ $file }}" target="_blank">{{ $file }}</a></li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@push('scripts')
<script>
    const statusEl = document.getElementById('sitemap-status');
    const btn = document.getElementById('start-sitemap');
    let running = false;
    let stepsPerCall = 10;

    btn.addEventListener('click', async () => {
        if (running) return;
        running = true;
        statusEl.innerText = 'Start generowania...';
        await fetch('{{ route('admin.sitemap.start') }}', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            credentials:'same-origin'
        });
        runChunk();
    });

    document.getElementById('clear-sitemap').addEventListener('click', async () => {
        if (running) return;
        statusEl.innerText = 'Usuwam stare sitemap...';
        const res = await fetch('{{ route('admin.sitemap.clear') }}', {
            method:'POST',
            headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            credentials:'same-origin'
        });
        if (res.ok) {
            statusEl.innerText = 'Usunięto wszystkie sitemap.';
        } else {
            statusEl.innerText = 'Błąd przy usuwaniu sitemap.';
        }
    });

    async function runChunk() {
        const res = await fetch('{{ route('admin.sitemap.run') }}', {
            method:'POST',
            headers:{
                'X-CSRF-TOKEN':'{{ csrf_token() }}',
                'Content-Type':'application/json',
                'Accept':'application/json'
            },
            credentials:'same-origin',
            body: JSON.stringify({steps: stepsPerCall})
        });
        if (res.status === 403) {
            statusEl.innerText = 'Sesja wygasła lub brak dostępu (403). Odśwież stronę i zaloguj się ponownie.';
            running = false;
            return;
        }
        if (!res.ok) {
            statusEl.innerText = 'Błąd podczas generowania.';
            running = false;
            return;
        }
        const data = await res.json();
        if (data.status === 'running') {
            statusEl.innerText = `Przetwarzanie... plik: ${data.file} (łącznie ${data.files_count}), zapisanych rekordów: ${data.processed}`;
            setTimeout(runChunk, 400);
        } else if (data.status === 'finished') {
            statusEl.innerText = `Zakończono generowanie sitemap. Przetworzono ${data.total_processed} rekordów.`;
            running = false;
        } else {
            statusEl.innerText = 'Brak aktywnego zadania.';
            running = false;
        }
    }
</script>
@endpush
@endsection
