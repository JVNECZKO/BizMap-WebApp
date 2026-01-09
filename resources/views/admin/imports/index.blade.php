@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Import</p>
        <h1 class="text-2xl font-bold text-slate-900">Silnik importu hurtowego</h1>
    </div>
    <div class="text-sm text-slate-500">Formaty: CSV, JSON, XML, XLS, XLSX</div>
</div>

<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div class="p-6 bg-white shadow-panel rounded-2xl border border-slate-100">
            <p class="text-sm font-semibold text-slate-800 mb-3">1. Wgraj plik</p>
            <form id="upload-form" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="file" name="file" accept=".csv,.json,.xml,.xls,.xlsx" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white shadow-panel">Podgląd</button>
            </form>
            <div id="upload-status" class="text-sm text-slate-600 mt-3"></div>
        </div>

        <div class="p-6 bg-white shadow-panel rounded-2xl border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-slate-800">2. Mapowanie kolumn</p>
                <div class="flex items-center gap-3">
                    <select id="load-template" class="text-sm rounded-lg border border-slate-200 px-3 py-2">
                        <option value="">Wczytaj szablon…</option>
                        @foreach($mappings as $map)
                            <option value="{{ $map->id }}">{{ $map->name }}</option>
                        @endforeach
                    </select>
                    <button id="save-template" class="text-sm text-sky-700">Zapisz szablon</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm" id="mapping-table">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                        <tr>
                            <th class="px-3 py-2 text-left">Kolumna źródłowa</th>
                            <th class="px-3 py-2 text-left">Pole kanoniczne</th>
                            <th class="px-3 py-2 text-left">Stała wartość (opcjonalnie)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="mapping-body">
                        <tr><td colspan="3" class="px-3 py-4 text-slate-500">Wgraj plik, aby zobaczyć kolumny.</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex gap-3">
                <button id="start-import" class="px-4 py-2 rounded-xl bg-emerald-600 text-white shadow-panel disabled:opacity-50" disabled>Start importu</button>
                <div class="text-sm text-slate-500" id="import-hint">Najpierw wgraj plik i ustaw mapowanie.</div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 rounded-full h-3">
                    <div id="progress-bar" class="h-3 rounded-full bg-sky-500 transition-all" style="width:0%"></div>
                </div>
                <p id="progress-text" class="text-sm text-slate-600 mt-2">0% | 0 rekordów</p>
            </div>
        </div>

        <div class="p-6 bg-white shadow-panel rounded-2xl border border-slate-100">
            <p class="text-sm font-semibold text-slate-800 mb-3">Podgląd danych</p>
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs" id="preview-table"></table>
            </div>
        </div>
    </div>
    <div class="space-y-5">
        <div class="p-5 bg-white shadow-panel rounded-2xl border border-slate-100">
            <p class="text-sm font-semibold text-slate-800 mb-2">Szablony</p>
            <ul class="space-y-2 text-sm">
                @forelse($mappings as $map)
                    <li class="flex items-center justify-between">
                        <span>{{ $map->name }}</span>
                        <a class="text-sky-700 text-xs" href="{{ route('admin.mappings.export', $map) }}">Eksport</a>
                    </li>
                @empty
                    <li class="text-slate-500">Brak zapisanych szablonów.</li>
                @endforelse
            </ul>
            <form action="{{ route('admin.mappings.import') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-2">
                @csrf
                <input type="file" name="file" class="w-full text-xs">
                <button class="px-3 py-2 rounded-lg border border-slate-200 text-sm">Importuj szablon</button>
            </form>
        </div>
        <div class="p-5 bg-white shadow-panel rounded-2xl border border-slate-100">
            <p class="text-sm font-semibold text-slate-800 mb-2">Historia importów</p>
            <div class="space-y-3 text-sm text-slate-700 max-h-80 overflow-auto">
                @forelse($logs as $log)
                    <div class="border border-slate-100 rounded-xl p-3">
                        <p class="font-semibold">{{ $log->filename }}</p>
                        <p class="text-xs text-slate-500">Format: {{ $log->format }} • Status: {{ $log->status }}</p>
                        <p class="text-xs text-slate-500">Wiersze: {{ $log->imported_rows }} / {{ $log->total_rows }}</p>
                    </div>
                @empty
                    <p class="text-slate-500">Brak historii.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @php
        $savedMappings = $mappings
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'mapping' => $m->mapping,
                    'static_values' => $m->static_values,
                ];
            })
            ->values();
    @endphp

    const adminPrefix = "{{ config('bizmap.admin_prefix') }}";
    let sessionToken = null;
    let selectedTemplateId = null;
    const savedMappings = {!! $savedMappings->toJson() !!};
    const canonicalFields = [
        'lp','nip','regon','full_name','nazwisko','imie','telefon','email','adres_www','wojewodztwo','powiat','gmina','miejscowosc','ulica','nr_budynku','nr_lokalu','kod_pocztowy','glowny_kod_pkd','pozostale_kody_pkd','rok_pkd','status_dzialalnosci','data_rozpoczecia_dzialalnosci','data_zakonczenia_dzialalnosci','data_zawieszenia_dzialalnosci','data_wznowienia_dzialalnosci'
    ];

    document.getElementById('upload-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        document.getElementById('upload-status').innerText = 'Wczytywanie...';
        const res = await fetch(`/${adminPrefix}/importy/upload`, { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body: formData });
        if (!res.ok) {
            document.getElementById('upload-status').innerText = 'Błąd wgrywania pliku.';
            return;
        }
        const data = await res.json();
        sessionToken = data.token;
        document.getElementById('upload-status').innerText = 'Plik wczytany. Skonfiguruj mapowanie.';
        renderPreview(data.preview);
        renderMapping(data.columns);
        document.getElementById('start-import').disabled = false;
    });

    function renderPreview(rows) {
        const table = document.getElementById('preview-table');
        if (!rows || !rows.length) {
            table.innerHTML = '<tr><td class="px-3 py-2 text-slate-500">Brak podglądu.</td></tr>';
            return;
        }
        const headers = Object.keys(rows[0]);
        let html = '<thead><tr>';
        headers.forEach(h => html += `<th class="px-3 py-2 bg-slate-50 text-slate-600">${h}</th>`);
        html += '</tr></thead><tbody>';
        rows.forEach(row => {
            html += '<tr class="divide-x divide-slate-100">';
            headers.forEach(h => html += `<td class="px-3 py-2 text-slate-700">${row[h] ?? ''}</td>`);
            html += '</tr>';
        });
        html += '</tbody>';
        table.innerHTML = html;
    }

    function renderMapping(columns) {
        const body = document.getElementById('mapping-body');
        body.innerHTML = '';
        let hasWoj = false;
        columns.forEach(col => {
            if (col.toLowerCase().includes('wojew')) {
                hasWoj = true;
            }
            const key = col;
            const select = canonicalFields.map(f => `<option value="${f}">${f}</option>`).join('');
            body.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="px-3 py-2 text-slate-800 font-semibold">${key}</td>
                    <td class="px-3 py-2">
                        <select data-column="${key}" class="w-full rounded-lg border border-slate-200 px-2 py-2">
                            <option value="ignore">Ignoruj</option>
                            ${select}
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" data-static-for="${key}" placeholder="wartość stała (opcjonalnie)" class="w-full rounded-lg border border-slate-200 px-2 py-2 text-sm">
                    </td>
                </tr>
            `);
        });

        if (!hasWoj) {
            body.insertAdjacentHTML('beforeend', `
                <tr class="bg-slate-50">
                    <td class="px-3 py-2 text-slate-700 font-semibold">[Stała] województwo</td>
                    <td class="px-3 py-2">
                        <select data-column="static-wojewodztwo" class="w-full rounded-lg border border-slate-200 px-2 py-2">
                            <option value="wojewodztwo" selected>wojewodztwo</option>
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <input type="text" data-static-for="static-wojewodztwo" placeholder="np. dolnośląskie" class="w-full rounded-lg border border-slate-200 px-2 py-2 text-sm">
                    </td>
                </tr>
            `);
        }
    }

    document.getElementById('start-import').addEventListener('click', async () => {
        if (!sessionToken) return alert('Najpierw wgraj plik.');
        const mapping = {};
        const staticValues = {};
        document.querySelectorAll('[data-column]').forEach(select => {
            mapping[select.dataset.column] = select.value;
            const staticInput = document.querySelector(`[data-static-for="${select.dataset.column}"]`);
            if (staticInput && staticInput.value) {
                staticValues[select.value] = staticInput.value;
            }
        });

        await fetch(`/${adminPrefix}/importy/start`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({token: sessionToken, mapping_id: selectedTemplateId})
        });

        runChunk(mapping, staticValues);
    });

    async function runChunk(mapping, staticValues) {
        let data = {};
        try {
            const res = await fetch(`/${adminPrefix}/importy/run`, {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({
                    token: sessionToken,
                    mapping: mapping,
                    static_values: staticValues,
                    mapping_id: selectedTemplateId,
                })
            });
            data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || 'Błąd importu');
            }
        } catch (err) {
            document.getElementById('progress-text').innerText = err.message || 'Błąd importu (szczegóły w logach).';
            return;
        }
        const imported = data.imported ?? 0;
        const processed = data.processed ?? 0;
        const total = data.total_rows ?? 0;
        let percent = 0;
        if (total > 0) {
            percent = Math.min(100, Math.round((imported / total) * 100));
        } else {
            percent = data.status === 'finished' ? 100 : Math.min(95, Math.round((processed / {{ config('bizmap.import.chunk') }}) * 100));
        }
        document.getElementById('progress-bar').style.width = `${percent}%`;
        document.getElementById('progress-text').innerText = `${percent}% | ${imported} rekordów`;

        if (data.status !== 'finished') {
            setTimeout(() => runChunk(mapping, staticValues), 700);
        } else {
            document.getElementById('progress-text').innerText = `Zakończono import: ${imported} rekordów`;
        }
    }

    document.getElementById('save-template').addEventListener('click', async () => {
        const name = prompt('Nazwa szablonu?');
        if (!name) return;
        const mapping = {};
        const staticValues = {};
        document.querySelectorAll('[data-column]').forEach(select => {
            mapping[select.dataset.column] = select.value;
            const staticInput = document.querySelector(`[data-static-for="${select.dataset.column}"]`);
            if (staticInput && staticInput.value) {
                staticValues[select.value] = staticInput.value;
            }
        });
        await fetch(`/${adminPrefix}/importy/mappings`, {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({name, mapping, static_values: staticValues})
        });
        alert('Szablon zapisany.');
    });

    document.getElementById('load-template').addEventListener('change', (e) => {
        const id = e.target.value ? parseInt(e.target.value) : null;
        selectedTemplateId = id;
        if (!id) return;
        const template = savedMappings.find(m => m.id === id);
        if (!template) return;
        applyTemplate(template);
    });

    function applyTemplate(template) {
        const mapping = template.mapping || {};
        const staticValues = template.static_values || {};
        document.querySelectorAll('[data-column]').forEach(select => {
            const target = mapping[select.dataset.column];
            if (target) {
                select.value = target;
                const staticInput = document.querySelector(`[data-static-for="${select.dataset.column}"]`);
                if (staticInput && staticValues[target] !== undefined) {
                    staticInput.value = staticValues[target];
                }
            }
        });
        const wojStatic = document.querySelector('[data-static-for="static-wojewodztwo"]');
        if (wojStatic && staticValues['wojewodztwo']) {
            wojStatic.value = staticValues['wojewodztwo'];
        }
    }
</script>
@endpush
@endsection
