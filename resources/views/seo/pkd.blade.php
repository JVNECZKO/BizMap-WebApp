@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="glass rounded-3xl shadow-card p-8 mb-8">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-2">PKD {{ $pkd->code }}</p>
        <h1 class="text-3xl font-extrabold text-slate-900 mb-3">
            Firmy PKD {{ $pkd->code }} – {{ $pkd->name }} @if($regionName) w {{ $regionName }} @endif
        </h1>
        <p class="text-slate-600 mb-4">
            Zestawienie firm przypisanych do kodu PKD {{ $pkd->code }} ({{ $pkd->name }}). @if($regionName) Lista zawężona do województwa {{ $regionName }}.@else Możesz przejrzeć działalności w całym kraju lub zawęzić widok do wybranego regionu.@endif
        </p>
        <div class="flex flex-wrap gap-4 items-center mb-4">
            <div>
                <label class="text-xs uppercase tracking-[0.2em] text-slate-500 block mb-1">Wybierz kod PKD</label>
                <select id="pkd-select" class="rounded-xl border border-slate-200 px-4 py-2 text-sm min-w-[260px]">
                    @foreach($allCodes as $item)
                        <option value="{{ $item['code'] }}" data-slug="{{ $item['slug'] }}" @selected($item['code'] === $pkd->code)>
                            {{ $item['code'] }} — {{ $item['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($regionName)
                <div class="text-sm text-slate-600">Region: <span class="font-semibold">{{ $regionName }}</span></div>
            @endif
        </div>
        <div class="flex flex-wrap gap-3">
            @foreach($otherRegions as $item)
                <a href="{{ route('seo.pkd', ['code' => $pkd->code, 'slug' => \Illuminate\Support\Str::slug($pkd->code.' '.$pkd->name), 'region' => $item['slug']]) }}"
                   class="px-4 py-2 rounded-full border text-sm {{ ($regionSlug === $item['slug']) ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-700 hover:border-slate-400' }}">
                    {{ $item['name'] }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
            @foreach($companies as $company)
                <div class="bg-white shadow-card border border-slate-100 rounded-2xl p-5 flex justify-between items-center">
                    <div>
                        <a href="{{ route('company.show', ['id' => $company->id, 'slug' => $company->slug]) }}" class="text-lg font-semibold text-slate-900 hover:text-sky-700">
                            {{ $company->full_name }}
                        </a>
                        <p class="text-sm text-slate-600">
                            {{ $company->miejscowosc ?? 'Brak miejscowości' }} @if($company->wojewodztwo) • {{ $company->wojewodztwo }} @endif
                        </p>
                        <p class="text-xs text-slate-500 mt-1">PKD główny: {{ $company->glowny_kod_pkd ?? 'brak' }} • Status: {{ $company->status_dzialalnosci ?? 'brak' }}</p>
                    </div>
                    <a href="{{ route('company.show', ['id' => $company->id, 'slug' => $company->slug]) }}" class="text-sm text-sky-700 font-semibold hover:text-sky-900">
                        Szczegóły →
                    </a>
                </div>
            @endforeach

            @if($companies->isEmpty())
                <div class="bg-white border border-slate-100 rounded-2xl p-6 text-slate-600 shadow-card">
                    Brak firm dla tej kombinacji kodu PKD @if($regionName) i regionu {{ $regionName }}@endif.
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="bg-white border border-slate-100 rounded-2xl shadow-card p-5">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500 mb-3">Popularne regiony</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($otherRegions as $item)
                        <a href="{{ route('seo.pkd', ['code' => $pkd->code, 'slug' => \Illuminate\Support\Str::slug($pkd->code.' '.$pkd->name), 'region' => $item['slug']]) }}"
                           class="px-3 py-2 rounded-lg border text-xs {{ ($regionSlug === $item['slug']) ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-700 hover:border-slate-400' }}">
                            {{ $item['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl shadow-card p-5">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500 mb-3">Inne kody PKD</p>
                <ul class="space-y-2 text-sm">
                    @foreach($relatedCodes as $item)
                        <li>
                            <a class="text-sky-700 hover:text-sky-900 font-semibold" href="{{ route('seo.pkd', ['code' => $item['code'], 'slug' => $item['slug']]) }}">
                                {{ $item['code'] }} – {{ $item['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-card">
                <p class="text-sm font-semibold mb-2">Potrzebujesz innej lokalizacji?</p>
                <p class="text-sm text-slate-200 mb-3">Przeglądaj firmy z kodem PKD {{ $pkd->code }} w innych regionach – każda strona jest gotowa do szybkiego sprawdzenia kontrahenta.</p>
                <a href="{{ url('/pkd/' . $pkd->code . '/' . \Illuminate\Support\Str::slug($pkd->code.' '.$pkd->name)) }}" class="inline-block bg-white text-slate-900 font-semibold px-4 py-2 rounded-lg shadow mt-1">Widok ogólnopolski</a>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    (() => {
        const select = document.getElementById('pkd-select');
        const currentRegion = @json($regionSlug);

        select?.addEventListener('change', () => {
            const option = select.options[select.selectedIndex];
            const code = option.value;
            const slug = option.dataset.slug;
            let url = `/pkd/${encodeURIComponent(code)}/${slug}`;
            if (currentRegion) {
                url += `/${currentRegion}`;
            }
            window.location.href = url;
        });
    })();
</script>
@endpush
@endsection
