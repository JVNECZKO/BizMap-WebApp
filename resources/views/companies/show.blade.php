@extends('layouts.public')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Profil firmy</p>
            <h1 class="text-3xl font-bold text-slate-900">{{ $business->full_name }}</h1>
            <p class="text-sm text-slate-600">Źródło: CEIDG • PKD {{ $pkdVersion }}</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-xs bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-slate-700">
                Przeglądasz <span class="font-semibold">{{ max(1, $samePkd->count()) }}</span> firm{{ $samePkd->count() === 1 ? 'ę' : 'y' }} z tej branży
            </div>
            <a href="{{ route('companies.index') }}" class="text-sm text-sky-700 hover:text-sky-900">← Wróć do wyszukiwarki</a>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-5 mb-8">
        <div class="p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Identyfikatory</p>
            <p class="mt-2 text-sm text-slate-700">NIP: <span class="font-semibold">{{ $business->nip ?? 'brak' }}</span></p>
            <p class="text-sm text-slate-700">REGON: <span class="font-semibold">{{ $business->regon ?? 'brak' }}</span></p>
            <p class="text-sm text-slate-700">Osoba: <span class="font-semibold">{{ trim(($business->imie ?? '') . ' ' . ($business->nazwisko ?? '')) ?: 'brak' }}</span></p>
            <p class="text-sm text-slate-700">Status: <span class="font-semibold">{{ $business->status_dzialalnosci ?? 'brak' }}</span></p>
        </div>
        <div class="p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Adres</p>
            <p class="mt-2 text-sm text-slate-700">{{ $business->ulica ?? '' }} {{ $business->nr_budynku ?? '' }} {{ $business->nr_lokalu ? '/'.$business->nr_lokalu : '' }}</p>
            <p class="text-sm text-slate-700">{{ $business->kod_pocztowy ?? '' }} {{ $business->miejscowosc ?? '' }}</p>
            <p class="text-sm text-slate-700">{{ $business->gmina ?? '' }}, {{ $business->powiat ?? '' }}, {{ $business->wojewodztwo ?? '' }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Dane kontaktowe</p>
            <p class="mt-2 text-sm text-slate-700">Telefon: {{ $business->telefon ?? 'brak' }}</p>
            <p class="text-sm text-slate-700">Email: {{ $business->email ?? 'brak' }}</p>
            <p class="text-sm text-slate-700">WWW: {{ $business->adres_www ?? 'brak' }}</p>
        </div>
    </div>

    <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 mb-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD</p>
                <div class="mt-3 flex flex-wrap gap-3">
                    @if($business->glowny_kod_pkd)
                        <span class="px-3 py-2 rounded-xl bg-slate-900 text-white text-sm shadow-card">
                            {{ $business->glowny_kod_pkd }} — {{ $pkdNames[$business->glowny_kod_pkd]->name ?? 'kod główny' }}
                        </span>
                    @endif
                    @foreach($business->pkdCodes as $code)
                        @if($code->pkd_code !== $business->glowny_kod_pkd)
                            <span class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm">
                                {{ $code->pkd_code }} {{ $pkdNames[$code->pkd_code]->name ?? '' }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
            @if($business->glowny_kod_pkd && $pkdSlug)
                <a href="{{ route('seo.pkd', ['code' => $business->glowny_kod_pkd, 'slug' => $pkdSlug]) }}"
                   class="px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold shadow-card hover:bg-slate-800 text-sm">
                    Zobacz więcej firm z tej branży
                </a>
            @endif
        </div>
    </div>

    <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Terminy</p>
        <div class="grid md:grid-cols-4 gap-4 mt-3 text-sm text-slate-700">
            <div>
                <p class="text-slate-500">Rozpoczęcie</p>
                <p class="font-semibold">{{ optional($business->data_rozpoczecia_dzialalnosci)->format('Y-m-d') ?? 'brak' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Zawieszenie</p>
                <p class="font-semibold">{{ optional($business->data_zawieszenia_dzialalnosci)->format('Y-m-d') ?? 'brak' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Wznowienie</p>
                <p class="font-semibold">{{ optional($business->data_wznowienia_dzialalnosci)->format('Y-m-d') ?? 'brak' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Zakończenie</p>
                <p class="font-semibold">{{ optional($business->data_zakonczenia_dzialalnosci)->format('Y-m-d') ?? 'brak' }}</p>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-5 mt-8">
        <div class="md:col-span-1 p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-3">Inne firmy z tej samej branży (PKD)</p>
            @if($samePkd->isEmpty())
                <p class="text-sm text-slate-500">Brak danych.</p>
            @else
                <ul class="space-y-2 text-sm text-slate-800">
                    @foreach($samePkd as $item)
                        <li>
                            <a class="hover:text-sky-700" href="{{ route('company.show', ['id' => $item->id, 'slug' => $item->slug]) }}">
                                {{ $item->full_name }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $item->miejscowosc ?? '' }} {{ $item->powiat ? '• '.$item->powiat : '' }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="md:col-span-1 p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-3">Firmy w tej samej miejscowości</p>
            @if($sameCity->isEmpty())
                <p class="text-sm text-slate-500">Brak danych.</p>
            @else
                <ul class="space-y-2 text-sm text-slate-800">
                    @foreach($sameCity as $item)
                        <li>
                            <a class="hover:text-sky-700" href="{{ route('company.show', ['id' => $item->id, 'slug' => $item->slug]) }}">
                                {{ $item->full_name }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $item->glowny_kod_pkd ?? '' }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="md:col-span-1 p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-3">Podobne działalności w regionie</p>
            @if($sameRegion->isEmpty())
                <p class="text-sm text-slate-500">Brak danych.</p>
            @else
                <ul class="space-y-2 text-sm text-slate-800">
                    @foreach($sameRegion as $item)
                        <li>
                            <a class="hover:text-sky-700" href="{{ route('company.show', ['id' => $item->id, 'slug' => $item->slug]) }}">
                                {{ $item->full_name }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $item->miejscowosc ?? '' }} {{ $item->glowny_kod_pkd ? '• '.$item->glowny_kod_pkd : '' }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-5 mt-8">
        <div class="p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-3">Ostatnio przeglądane firmy</p>
            <ul id="recently-viewed" class="space-y-2 text-sm text-slate-800">
                <li class="text-slate-500 text-xs" id="recently-empty">Brak historii.</li>
            </ul>
        </div>
        <div class="p-5 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-3">Najczęściej sprawdzane w tym tygodniu</p>
            @if($hotBusinesses->isEmpty())
                <p class="text-sm text-slate-500">Brak danych.</p>
            @else
                <ul class="space-y-2 text-sm text-slate-800">
                    @foreach($hotBusinesses as $item)
                        <li class="flex justify-between items-start gap-3">
                            <div>
                                <a class="hover:text-sky-700 font-semibold" href="{{ route('company.show', ['id' => $item->id, 'slug' => $item->slug]) }}">{{ $item->full_name }}</a>
                                <p class="text-xs text-slate-500">{{ $item->miejscowosc ?? '' }} @if($item->wojewodztwo) • {{ $item->wojewodztwo }} @endif</p>
                            </div>
                            <span class="text-xs text-slate-500">{{ $item->glowny_kod_pkd ?? '' }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@push('scripts')
<script>
    (() => {
        const storageKey = 'bizmap_recent_firms';
        const maxItems = 8;
        const listEl = document.getElementById('recently-viewed');
        const emptyEl = document.getElementById('recently-empty');

        function loadList() {
            try {
                return JSON.parse(localStorage.getItem(storageKey) || '[]');
            } catch (e) {
                return [];
            }
        }

        function saveList(items) {
            localStorage.setItem(storageKey, JSON.stringify(items.slice(0, maxItems)));
        }

        function render(items) {
            if (!listEl) return;
            listEl.innerHTML = '';
            if (!items.length) {
                const li = document.createElement('li');
                li.className = 'text-slate-500 text-xs';
                li.textContent = 'Brak historii.';
                listEl.appendChild(li);
                return;
            }
            items.forEach(item => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="hover:text-sky-700 font-semibold" href="/firma/${item.id}-${item.slug}">${item.name}</a>
                                <p class="text-xs text-slate-500">${item.meta || ''}</p>`;
                listEl.appendChild(li);
            });
        }

        const current = {
            id: '{{ $business->id }}',
            slug: '{{ $business->slug }}',
            name: @json($business->full_name),
            meta: @json(trim(($business->miejscowosc ?? '') . ' ' . ($business->wojewodztwo ? '• '.$business->wojewodztwo : ''))),
        };

        let items = loadList().filter(i => i.id != current.id);
        items.unshift(current);
        saveList(items);
        render(items);
    })();
</script>
@endpush
@endsection
