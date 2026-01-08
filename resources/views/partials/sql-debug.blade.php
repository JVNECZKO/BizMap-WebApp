<div class="max-w-7xl mx-auto px-6 py-6 mt-8">
    <div class="rounded-2xl border border-slate-200 bg-white shadow-card">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500">SQL debug</p>
                <p class="text-sm text-slate-600">Zapytania w tej odpowiedzi ({{ count($queries) }}) • Łącznie {{ number_format($total, 2) }} ms</p>
            </div>
        </div>
        <div class="max-h-[360px] overflow-y-auto divide-y divide-slate-100">
            @forelse($queries as $q)
                <div class="px-4 py-3 text-sm">
                    <p class="text-slate-800 font-mono break-words">{{ $q['sql'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ number_format($q['time'], 2) }} ms</p>
                </div>
            @empty
                <div class="px-4 py-3 text-sm text-slate-500">Brak zapytań.</div>
            @endforelse
        </div>
    </div>
</div>
