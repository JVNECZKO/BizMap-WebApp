@extends('layouts.public')

@section('content')
<section class="relative overflow-hidden">
    <div class="max-w-6xl mx-auto px-6 py-12 lg:py-16">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-900 text-white text-xs uppercase tracking-[0.25em]">Rejestr CEIDG – BizMap</div>
                <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">BizMap to publiczny rejestr firm oparty na danych CEIDG.</h1>
                <p class="text-lg text-slate-600 leading-relaxed">Wyszukuj działalności według branży (PKD 2007 / 2025), lokalizacji i statusu wpisu. Bez kont i rejestracji – wiarygodne informacje dostępne od ręki.</p>
                <p class="text-sm text-slate-500">Bez kont. Bez rejestracji. Tylko rzetelne dane.</p>
                <form action="{{ route('companies.index') }}" method="GET" class="glass rounded-2xl shadow-card p-4 flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Szukaj po nazwie lub NIP</label>
                        <input type="text" name="q" placeholder="np. studio, software, sklep internetowy, 521-..." class="w-full mt-2 rounded-xl border border-slate-200 px-4 py-3 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    </div>
                    <div class="flex items-end">
                        <button class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-card hover:-translate-y-0.5 transition">Szukaj</button>
                    </div>
                </form>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-2xl bg-white shadow-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">10M+</p>
                        <p class="text-2xl font-bold text-slate-900">firm</p>
                        <p class="text-sm text-slate-600">dostępnych do przeglądania</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white shadow-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Aktualne dane CEIDG</p>
                        <p class="text-2xl font-bold text-slate-900">Wiarygodne</p>
                        <p class="text-sm text-slate-600">w czytelnej, uporządkowanej formie</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white shadow-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD 2007 / 2025</p>
                        <p class="text-2xl font-bold text-slate-900">Precyzyjne filtry</p>
                        <p class="text-sm text-slate-600">zgodne z klasyfikacją PKD</p>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="glass rounded-3xl shadow-card p-6 border border-slate-100">
                    <p class="text-sm text-slate-500 mb-4">Kto korzysta z BizMap?</p>
                    <ul class="space-y-3 text-slate-700">
                        <li class="flex items-start gap-3">
                            <span class="h-2.5 w-2.5 mt-2 rounded-full bg-sky-500"></span>
                            <div>
                                <p class="font-semibold">Firmy ubezpieczeniowe i instytucje finansowe</p>
                                <p class="text-sm text-slate-500">Weryfikacja kontrahentów, status działalności, analiza profilu firmy przed współpracą.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="h-2.5 w-2.5 mt-2 rounded-full bg-sky-500"></span>
                            <div>
                                <p class="font-semibold">Działy sprzedaży i zespoły B2B</p>
                                <p class="text-sm text-slate-500">Identyfikacja klientów według branży, regionu i typu działalności.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="h-2.5 w-2.5 mt-2 rounded-full bg-sky-500"></span>
                            <div>
                                <p class="font-semibold">Media, portale lokalne i wydawcy branżowi</p>
                                <p class="text-sm text-slate-500">Katalogi firm, profile przedsiębiorców, sekcje „lokalne firmy”.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="h-2.5 w-2.5 mt-2 rounded-full bg-sky-500"></span>
                            <div>
                                <p class="font-semibold">Analitycy rynku i doradcy biznesowi</p>
                                <p class="text-sm text-slate-500">Struktura branżowa, nasycenie rynku, trendy w sektorach i regionach.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="h-2.5 w-2.5 mt-2 rounded-full bg-sky-500"></span>
                            <div>
                                <p class="font-semibold">Przedsiębiorcy i planujący działalność</p>
                                <p class="text-sm text-slate-500">Sprawdzenie konkurencji, analiza lokalnego rynku, weryfikacja partnerów.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white border-t border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-6 py-12 grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-2xl bg-slate-900 text-white shadow-card">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-300">Wiarygodność</p>
            <p class="text-2xl font-bold mt-3">Dane z CEIDG</p>
            <p class="text-sm text-slate-200 mt-2">Ładowane hurtowo, walidowane daty, osobna tabela z surowym payloadem każdej pozycji.</p>
        </div>
        <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Aktualność</p>
            <p class="text-xl font-semibold mt-2">Regularne uaktualnienia</p>
            <p class="text-sm text-slate-500 mt-2">Dane z CEIDG prezentowane w spójnej, uporządkowanej formie.</p>
        </div>
        <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Dostępność</p>
            <p class="text-xl font-semibold mt-2">Publiczny rejestr</p>
            <p class="text-sm text-slate-500 mt-2">Otwarte, przejrzyste informacje o firmach dla wszystkich użytkowników.</p>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-6 py-14">
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Nowe firmy w bazie</p>
            <h2 class="text-3xl font-bold text-slate-900">Przegląd najnowszych wpisów</h2>
        </div>
        <a href="{{ route('companies.index') }}" class="text-sm text-sky-700 hover:text-sky-900">Przeglądaj wszystkie</a>
    </div>
    <div class="grid md:grid-cols-3 gap-5">
        @forelse($latest as $biz)
            <div class="p-5 rounded-2xl bg-white shadow-card border border-slate-100">
                <p class="text-sm font-semibold text-slate-900">{{ $biz->full_name }}</p>
                <p class="text-xs text-slate-500 mt-1">NIP {{ $biz->nip ?? 'brak' }} • REGON {{ $biz->regon ?? 'brak' }}</p>
                <p class="text-sm text-slate-600 mt-2">{{ $biz->miejscowosc ?? '–' }}, {{ $biz->powiat ?? '–' }}</p>
                <a href="{{ route('company.show', ['id' => $biz->id, 'slug' => $biz->slug]) }}" class="mt-3 inline-flex items-center gap-2 text-sm text-sky-700">Profil <span>→</span></a>
            </div>
        @empty
            <p class="text-slate-500">Brak danych do wyświetlenia.</p>
        @endforelse
    </div>
</section>

<section class="bg-slate-900 text-white">
    <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Popularne branże (PKD)</p>
                <h2 class="text-3xl font-bold">Najczęściej występujące kody</h2>
            </div>
            <p class="text-sm text-slate-300">Wersja PKD: {{ $pkdNames->first()->version ?? '2007' }}</p>
        </div>
        <div class="grid md:grid-cols-4 gap-4 mt-6">
            @forelse($popular as $row)
                @php
                    $label = $pkdNames[$row->pkd_code]->name ?? $row->pkd_code;
                @endphp
                <div class="p-4 rounded-xl bg-white/5 border border-white/10">
                    <p class="text-sm text-slate-200">{{ $row->pkd_code }}</p>
                    <p class="text-lg font-semibold text-white mt-1">{{ $label }}</p>
                    <p class="text-xs text-slate-300 mt-1">{{ $row->total }} wpisów</p>
                </div>
            @empty
                <p class="text-slate-300">Brak danych PKD.</p>
            @endforelse
        </div>
        <a href="{{ route('pkd.index') }}" class="mt-6 inline-flex items-center gap-2 text-sm text-white underline">Zobacz wszystkie branże</a>
    </div>
</section>

<section class="max-w-6xl mx-auto px-6 py-12">
    <div class="grid md:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Jak korzystać</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-2">Jak korzystać z BizMap?</h3>
            <ol class="mt-4 space-y-3 text-slate-600 list-decimal list-inside">
                <li>Wpisz nazwę firmy, numer NIP lub wybierz branżę PKD.</li>
                <li>Zawęź wyniki według lokalizacji lub statusu działalności.</li>
                <li>Przeglądaj listę firm lub szczegóły wybranego wpisu.</li>
            </ol>
            <p class="mt-4 text-sm text-slate-600">Każda firma ma własny profil z kluczowymi identyfikatorami i danymi adresowymi.</p>
        </div>
        <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Dlaczego BizMap?</p>
            <h3 class="text-2xl font-bold text-slate-900 mt-2">Przejrzyste dane</h3>
            <ul class="mt-4 space-y-3 text-slate-600">
                <li>• Oficjalne źródło: CEIDG</li>
                <li>• Spójne dane adresowe i identyfikacyjne</li>
                <li>• Pełne opisy PKD (2007 / 2025)</li>
                <li>• Publiczny dostęp bez rejestracji</li>
            </ul>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-6 pb-12">
    <div class="rounded-2xl bg-white shadow-card border border-slate-100 p-6">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Informacje prawne</p>
        <p class="text-slate-700 mt-2">Dane mają charakter informacyjny. Użytkownik podejmuje decyzje biznesowe na własną odpowiedzialność.</p>
        <p class="text-slate-700 mt-1">Źródło danych: Centralna Ewidencja i Informacja o Działalności Gospodarczej (CEIDG).</p>
        <p class="text-slate-700 mt-1">Ostatnia aktualizacja danych: <span class="font-semibold">({{ now()->format('Y-m-d') }})</span></p>
    </div>
</section>
@endsection
