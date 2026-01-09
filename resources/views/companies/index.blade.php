@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Wyszukiwarka</p>
            <h1 class="text-3xl font-bold text-slate-900">Baza firm</h1>
            <p class="text-sm text-slate-600">Filtruj po nazwie, NIP, lokalizacji, statusie i PKD ({{ $pkdVersion }}).</p>
        </div>
        <a href="{{ route('landing') }}" class="text-sm text-sky-700 hover:text-sky-900">Wróć na stronę główną</a>
    </div>

    <form method="GET" class="glass rounded-2xl shadow-card p-6 mb-8 grid md:grid-cols-3 gap-4">
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Nazwa / NIP</label>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3" placeholder="np. studio, 525..." />
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Imię</label>
            <input type="text" name="imie" value="{{ $filters['imie'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3" placeholder="np. Anna" />
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Nazwisko</label>
            <input type="text" name="nazwisko" value="{{ $filters['nazwisko'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3" placeholder="np. Kowalski" />
        </div>
        <div class="relative">
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD</label>
            <div class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 bg-white" id="pkd-multiselect">
                <div class="flex flex-wrap gap-2" id="pkd-tags"></div>
                <input type="text" id="pkd-input" class="w-full border-0 focus:ring-0 focus:outline-none text-sm" placeholder="wpisz lub wybierz kody PKD...">
            </div>
            <div id="pkd-dropdown" class="absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-xl shadow-card max-h-60 overflow-y-auto hidden">
                <div class="p-2 border-b border-slate-100">
                    <input type="text" id="pkd-search" class="w-full border border-slate-200 rounded-lg px-2 py-1 text-sm" placeholder="Szukaj kodu...">
                </div>
                <div id="pkd-list" class="p-2 text-sm text-slate-800 space-y-1"></div>
            </div>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Status</label>
            <select name="status" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="">Dowolny</option>
                @foreach($filterOptions['statusy'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Województwo</label>
            <select id="wojewodztwo" name="wojewodztwo" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="">Dowolne</option>
                @foreach($filterOptions['wojewodztwa'] as $opt)
                    <option value="{{ $opt }}" @selected(($filters['wojewodztwo'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Powiat</label>
            <select id="powiat" name="powiat" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="">Dowolny</option>
                @foreach($filterOptions['powiaty'] as $opt)
                    <option value="{{ $opt }}" @selected(($filters['powiat'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Gmina</label>
            <select id="gmina" name="gmina" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="">Dowolna</option>
                @foreach($filterOptions['gminy'] as $opt)
                    <option value="{{ $opt }}" @selected(($filters['gmina'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Miejscowość</label>
            <select id="miejscowosc" name="miejscowosc" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="">Dowolna</option>
                @foreach($filterOptions['miejscowosci'] as $opt)
                    <option value="{{ $opt }}" @selected(($filters['miejscowosc'] ?? '') === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Kod pocztowy</label>
            <input type="text" name="kod_pocztowy" value="{{ $filters['kod_pocztowy'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3" placeholder="00-001" />
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Data rozpoczęcia od</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" />
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Data rozpoczęcia do</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" />
        </div>
        <div class="flex items-end gap-3">
            <button class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-card">Filtruj</button>
            <a href="{{ route('companies.index') }}" class="px-4 py-3 rounded-xl border border-slate-200 text-slate-600">Wyczyść</a>
        </div>
    </form>

    <div class="glass rounded-2xl shadow-card p-6 mb-8">
        <h2 class="text-xl font-semibold text-slate-900 mb-3">Eksport wyników</h2>
        <p class="text-sm text-slate-600 mb-4">Wyeksportuj bieżące wyniki wyszukiwania do CSV, wybierając interesujące pola i dodatkowe ograniczenia.</p>
        <form method="GET" action="{{ route('companies.export') }}" class="grid md:grid-cols-3 gap-4">
            @foreach($filters as $key => $value)
                @if(is_array($value))
                    @foreach($value as $v)
                        @if(!is_null($v) && $v !== '')
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endif
                    @endforeach
                @else
                    @if(!is_null($value) && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endif
            @endforeach
            <div class="md:col-span-3">
                <p class="text-sm font-semibold text-slate-800 mb-2">Pola do eksportu</p>
                <div class="grid md:grid-cols-4 gap-2 text-sm text-slate-700">
                    @php
                        $exportable = [
                            'full_name' => 'Nazwa firmy',
                            'nip' => 'NIP',
                            'regon' => 'REGON',
                            'status_dzialalnosci' => 'Status',
                            'glowny_kod_pkd' => 'PKD',
                            'wojewodztwo' => 'Województwo',
                            'powiat' => 'Powiat',
                            'gmina' => 'Gmina',
                            'miejscowosc' => 'Miejscowość',
                            'ulica' => 'Ulica',
                            'nr_budynku' => 'Nr budynku',
                            'nr_lokalu' => 'Nr lokalu',
                            'kod_pocztowy' => 'Kod pocztowy',
                            'telefon' => 'Telefon',
                            'email' => 'Email',
                            'adres_www' => 'WWW',
                            'data_rozpoczecia_dzialalnosci' => 'Data rozpoczęcia',
                            'data_zawieszenia_dzialalnosci' => 'Data zawieszenia',
                            'data_wznowienia_dzialalnosci' => 'Data wznowienia',
                        ];
                    @endphp
                    @foreach($exportable as $field => $label)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="fields[]" value="{{ $field }}" class="rounded border-slate-300">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="only_active" value="1" class="rounded border-slate-300">
                    Tylko aktywne działalności
                </label>
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="only_email" value="1" class="rounded border-slate-300">
                    Tylko z adresem e-mail
                </label>
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="only_phone" value="1" class="rounded border-slate-300">
                    Tylko z telefonem
                </label>
            </div>
            <div class="md:col-span-3">
                <button class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-card hover:-translate-y-0.5 transition">Eksportuj wyniki (CSV)</button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-card bg-white">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-[0.2em]">
                <tr>
                    <th class="px-4 py-3">Nazwa</th>
                    <th class="px-4 py-3">Osoba</th>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">PKD główne</th>
                    <th class="px-4 py-3">Lokalizacja</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($results as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $row->full_name }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ trim(($row->imie ?? '') . ' ' . ($row->nazwisko ?? '')) ?: '–' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $row->nip ?? '–' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $row->glowny_kod_pkd ?? '–' }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $row->miejscowosc ?? '–' }}, {{ $row->powiat ?? '' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $status = strtolower($row->status_dzialalnosci ?? '');
                                $statusClass = 'bg-slate-100 text-slate-700';
                                if (str_contains($status, 'aktyw')) $statusClass = 'bg-green-100 text-green-700';
                                elseif (str_contains($status, 'zawies')) $statusClass = 'bg-orange-100 text-orange-700';
                                elseif (str_contains($status, 'spółki cywilnej') || str_contains($status, 'spolki cywilnej')) $statusClass = 'bg-blue-100 text-blue-700';
                                elseif (str_contains($status, 'wykreśl') || str_contains($status, 'wykresl')) $statusClass = 'bg-red-100 text-red-700';
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                {{ $row->status_dzialalnosci ?? 'brak' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a class="text-sky-700 hover:text-sky-900" href="{{ route('company.show', ['id' => $row->id, 'slug' => $row->slug]) }}">Profil →</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-slate-500">Brak wyników.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between mt-6 text-sm flex-wrap gap-4">
        <form method="GET" class="flex items-center gap-2">
            @foreach($filters as $key => $value)
                @if(is_array($value))
                    @foreach($value as $v)
                        @if(!is_null($v) && $v !== '')
                            <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                        @endif
                    @endforeach
                @else
                    @if(!is_null($value) && $value !== '')
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endif
            @endforeach
            <label for="per_page" class="text-slate-600">Wyników na stronę:</label>
            <select name="per_page" id="per_page" class="rounded-lg border border-slate-200 px-3 py-2 text-slate-700" onchange="this.form.submit()">
                @php
                    $perPageOptions = [10,25,50,100,250,500,1000,2000,3000];
                    $currentPerPage = request('per_page', config('bizmap.pagination.per_page'));
                @endphp
                @foreach($perPageOptions as $opt)
                    <option value="{{ $opt }}" @selected($currentPerPage == $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </form>
        <div class="flex items-center gap-3">
            @if($results->previousPageUrl())
                <a class="px-4 py-2 rounded-lg border border-slate-200" href="{{ $results->previousPageUrl() }}">← Poprzednie</a>
            @else
                <span class="px-4 py-2 rounded-lg border border-slate-100 text-slate-400">← Poprzednie</span>
            @endif
            @if($results->nextPageUrl())
                <a class="px-4 py-2 rounded-lg bg-slate-900 text-white shadow-card" href="{{ $results->nextPageUrl() }}">Następne →</a>
            @else
                <span class="px-4 py-2 rounded-lg border border-slate-100 text-slate-400">Następne →</span>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const wojSelect = document.getElementById('wojewodztwo');
    const powiatSelect = document.getElementById('powiat');
    const gminaSelect = document.getElementById('gmina');
    const miejscowoscSelect = document.getElementById('miejscowosc');

    async function loadLocations() {
        if (!wojSelect.value) {
            fillSelect(powiatSelect, [], 'Dowolny', '');
            fillSelect(gminaSelect, [], 'Dowolna', '');
            fillSelect(miejscowoscSelect, [], 'Dowolna', '');
            setDisabled(powiatSelect, true);
            setDisabled(gminaSelect, true);
            setDisabled(miejscowoscSelect, true);
            return;
        }
        const params = new URLSearchParams();
        if (wojSelect.value) params.append('wojewodztwo', wojSelect.value);
        if (powiatSelect.value) params.append('powiat', powiatSelect.value);
        if (gminaSelect.value) params.append('gmina', gminaSelect.value);

        const res = await fetch(`/locations?${params.toString()}`);
        if (!res.ok) return;
        const data = await res.json();

        fillSelect(powiatSelect, data.powiaty, 'Dowolny', powiatSelect.value);
        fillSelect(gminaSelect, data.gminy, 'Dowolna', gminaSelect.value);
        fillSelect(miejscowoscSelect, data.miejscowosci, 'Dowolna', miejscowoscSelect.value);

        setDisabled(powiatSelect, data.powiaty.length === 0);
        setDisabled(gminaSelect, data.gminy.length === 0);
        setDisabled(miejscowoscSelect, data.miejscowosci.length === 0);
    }

    function fillSelect(select, items, placeholder, currentValue) {
        const val = currentValue || '';
        select.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        select.appendChild(opt);

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            if (item === val) option.selected = true;
            select.appendChild(option);
        });
    }

    function setDisabled(select, disabled) {
        select.disabled = disabled;
        if (disabled) {
            select.classList.add('bg-slate-100', 'cursor-not-allowed');
        } else {
            select.classList.remove('bg-slate-100', 'cursor-not-allowed');
        }
    }

    wojSelect?.addEventListener('change', () => {
        powiatSelect.value = '';
        gminaSelect.value = '';
        miejscowoscSelect.value = '';
        loadLocations();
    });
    powiatSelect?.addEventListener('change', () => {
        gminaSelect.value = '';
        miejscowoscSelect.value = '';
        loadLocations();
    });
    gminaSelect?.addEventListener('change', () => {
        miejscowoscSelect.value = '';
        loadLocations();
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadLocations();
    });

    // PKD multiselect
    const pkdInput = document.getElementById('pkd-input');
    const pkdDropdown = document.getElementById('pkd-dropdown');
    const pkdList = document.getElementById('pkd-list');
    const pkdTags = document.getElementById('pkd-tags');
    const pkdSearch = document.getElementById('pkd-search');
    let pkdOptions = [];
    let selectedPkd = @json((array)($filters['pkd'] ?? []));

    function renderTags() {
        pkdTags.innerHTML = '';
        selectedPkd.forEach(code => {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 text-slate-800 text-sm';
            tag.textContent = code;
            const close = document.createElement('button');
            close.type = 'button';
            close.textContent = '×';
            close.className = 'text-slate-500';
            close.onclick = () => removePkd(code);
            tag.appendChild(close);
            pkdTags.appendChild(tag);
        });
        document.querySelectorAll('input[name="pkd[]"]').forEach(el => el.remove());
        const form = pkdTags.closest('form');
        selectedPkd.forEach(code => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'pkd[]';
            hidden.value = code;
            form.appendChild(hidden);
        });
    }

    function removePkd(code) {
        selectedPkd = selectedPkd.filter(c => c !== code);
        renderTags();
        renderList();
    }

    function addPkd(code) {
        if (!selectedPkd.includes(code)) {
            selectedPkd.push(code);
            renderTags();
            renderList();
        }
    }

    function renderList() {
        const term = (pkdSearch.value || '').toLowerCase();
        pkdList.innerHTML = '';
        pkdOptions
            .filter(opt => opt.code.toLowerCase().includes(term) || (opt.name || '').toLowerCase().includes(term))
            .forEach(opt => {
                const row = document.createElement('label');
                row.className = 'flex items-center gap-2 p-1 rounded hover:bg-slate-50 cursor-pointer';
                const cb = document.createElement('input');
                cb.type = 'checkbox';
                cb.className = 'rounded border-slate-300';
                cb.checked = selectedPkd.includes(opt.code);
                cb.onchange = () => cb.checked ? addPkd(opt.code) : removePkd(opt.code);
                row.appendChild(cb);
                const text = document.createElement('div');
                text.innerHTML = `<p class="font-semibold">${opt.code}</p><p class="text-xs text-slate-500">${opt.name || ''}</p>`;
                row.appendChild(text);
                pkdList.appendChild(row);
            });
    }

    async function loadPkd() {
        const res = await fetch('/pkd-codes');
        if (!res.ok) return;
        pkdOptions = await res.json();
        renderList();
    }

    pkdInput?.addEventListener('focus', () => {
        pkdDropdown.classList.remove('hidden');
    });
    pkdInput?.addEventListener('keyup', (e) => {
        if (e.key === 'Enter' && pkdInput.value.trim() !== '') {
            addPkd(pkdInput.value.trim().toUpperCase());
            pkdInput.value = '';
        }
    });
    pkdSearch?.addEventListener('input', renderList);
    document.addEventListener('click', (e) => {
        if (!pkdDropdown.contains(e.target) && !pkdInput.contains(e.target) && !pkdTags.contains(e.target)) {
            pkdDropdown.classList.add('hidden');
        }
    });

    renderTags();
    loadPkd();
</script>
@endpush
