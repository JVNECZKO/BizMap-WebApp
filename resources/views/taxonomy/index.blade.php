@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Kategorie</p>
            <h1 class="text-3xl font-extrabold text-slate-900">Branże PKD</h1>
            <p class="text-slate-600">Przejrzyj branże zgrupowane według kodów PKD. Liczba firm liczona po kodach głównych.</p>
        </div>
    </div>

    <div class="space-y-5">
        @foreach($grouped as $group => $items)
            @php
                $groupTotal = $items->sum('computed_count');
            @endphp
            <div class="bg-white border border-slate-200 rounded-2xl shadow-card p-5">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                        <i class="fa-regular fa-folder-open"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-slate-900">{{ $group }}</h2>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">{{ number_format($groupTotal, 0, ',', ' ') }}</span>
                </div>
                <div class="flex flex-wrap gap-4 text-sm text-sky-800">
                    @foreach($items as $item)
                        <a class="hover:text-sky-900 font-semibold" href="{{ route('taxonomy.subgroup', [$item->group_slug, $item->subgroup_slug]) }}">
                            {{ $item->subgroup_name }}
                            <span class="text-slate-500 font-normal">({{ number_format($item->computed_count, 0, ',', ' ') }})</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
