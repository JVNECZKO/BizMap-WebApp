@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Branża</p>
            <h1 class="text-3xl font-extrabold text-slate-900">{{ $title }}</h1>
            <p class="text-slate-600">Lista firm przypisanych do wybranej kategorii.</p>
        </div>
        <a href="{{ route('taxonomy.public') }}" class="text-sm text-sky-700 hover:text-sky-900">← Wszystkie branże</a>
    </div>

    @if($siblings && $siblings->count() > 1)
        <div class="bg-white border border-slate-200 rounded-2xl shadow-card p-4 mb-6">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-2">Podgrupy</p>
            <div class="flex flex-wrap gap-3 text-sm">
                @foreach($siblings as $s)
                    <a href="{{ route('taxonomy.subgroup', [$s->group_slug, $s->subgroup_slug]) }}"
                       class="px-3 py-2 rounded-lg border {{ request()->route('subgroup') === $s->subgroup_slug ? 'bg-slate-900 text-white border-slate-900' : 'border-slate-200 text-slate-700 hover:border-slate-400' }}">
                        {{ $s->subgroup_name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-card p-4 mb-4">
        <form method="GET" class="flex items-center gap-3 text-sm">
            @foreach(request()->except('per_page','page') as $key => $val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endforeach
            <label class="text-slate-600">Wyników na stronę:</label>
            <select name="per_page" class="rounded-lg border border-slate-200 px-3 py-2" onchange="this.form.submit()">
                @foreach([25,50,100] as $opt)
                    <option value="{{ $opt }}" @selected($businesses->perPage() == $opt)>{{ $opt }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="space-y-3">
        @foreach($businesses as $company)
            <div class="bg-white border border-slate-200 rounded-2xl shadow-card p-4 flex justify-between items-center">
                <div>
                    <a href="{{ route('company.show', ['id' => $company->id, 'slug' => $company->slug]) }}" class="text-lg font-semibold text-slate-900 hover:text-sky-700">
                        {{ $company->full_name }}
                    </a>
                    <p class="text-sm text-slate-600">
                        {{ $company->miejscowosc ?? 'Brak miejscowości' }} @if($company->wojewodztwo) • {{ $company->wojewodztwo }} @endif
                    </p>
                    <p class="text-xs text-slate-500 mt-1">PKD: {{ $company->glowny_kod_pkd ?? 'brak' }} • Status: {{ $company->status_dzialalnosci ?? 'brak' }}</p>
                </div>
                <a href="{{ route('company.show', ['id' => $company->id, 'slug' => $company->slug]) }}" class="text-sm text-sky-700 font-semibold hover:text-sky-900">Szczegóły →</a>
            </div>
        @endforeach
    </div>

    <div class="flex items-center justify-between mt-6 text-sm flex-wrap gap-4">
        <div>
            Strona {{ $businesses->currentPage() }}
        </div>
        <div class="flex items-center gap-3">
            @if($businesses->previousPageUrl())
                <a href="{{ $businesses->previousPageUrl() }}" class="px-4 py-2 rounded-lg border border-slate-200">← Poprzednie</a>
            @endif
            @if($businesses->nextPageUrl())
                <a href="{{ $businesses->nextPageUrl() }}" class="px-4 py-2 rounded-lg border border-slate-200">Następne →</a>
            @endif
        </div>
    </div>
</div>
@endsection
