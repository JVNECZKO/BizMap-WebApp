@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Firmy</p>
        <h1 class="text-2xl font-bold text-slate-900">Zarządzanie wpisami</h1>
    </div>
    <form method="POST" action="{{ route('admin.businesses.wipe') }}" onsubmit="return confirm('Na pewno usunąć wszystkie wpisy? To działanie jest nieodwracalne.');">
        @csrf
        <button class="px-4 py-2 rounded-xl bg-red-600 text-white shadow-panel hover:bg-red-700">Usuń wszystkie wpisy</button>
    </form>
</div>

<form method="GET" class="grid md:grid-cols-3 gap-3 mb-4">
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Nazwa lub NIP" class="rounded-xl border border-slate-200 px-4 py-2">
    <select name="status" class="rounded-xl border border-slate-200 px-4 py-2">
        <option value="">Status dowolny</option>
        <option value="aktywny" @selected(($filters['status'] ?? '') === 'aktywny')>Aktywny</option>
        <option value="zawieszony" @selected(($filters['status'] ?? '') === 'zawieszony')>Zawieszony</option>
    </select>
    <button class="px-4 py-2 rounded-xl bg-slate-900 text-white">Filtruj</button>
</form>

<form method="POST" action="{{ route('admin.businesses.bulk-delete') }}" id="bulk-form">
    @csrf
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-panel">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-[0.2em]">
                <tr>
                    <th class="px-3 py-3"><input type="checkbox" onclick="toggleAll(this)"></th>
                    <th class="px-3 py-3 text-left">Nazwa</th>
                    <th class="px-3 py-3 text-left">NIP</th>
                    <th class="px-3 py-3 text-left">PKD</th>
                    <th class="px-3 py-3 text-left">Status</th>
                    <th class="px-3 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($businesses as $biz)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2"><input type="checkbox" name="ids[]" value="{{ $biz->id }}"></td>
                        <td class="px-3 py-2">{{ $biz->full_name }}</td>
                        <td class="px-3 py-2">{{ $biz->nip }}</td>
                        <td class="px-3 py-2">{{ $biz->glowny_kod_pkd }}</td>
                        <td class="px-3 py-2">{{ $biz->status_dzialalnosci }}</td>
                        <td class="px-3 py-2 flex items-center gap-3">
                            <a class="text-sky-700" href="{{ route('admin.businesses.edit', $biz) }}">Edytuj</a>
                            <button type="button" class="text-red-600" onclick="deleteSingle('{{ route('admin.businesses.destroy', $biz) }}')">Usuń</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">Brak wpisów.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between mt-4">
        <button type="submit" class="px-4 py-2 rounded-xl border border-slate-200" onclick="return confirm('Usunąć zaznaczone wpisy?')">Usuń zaznaczone</button>
        <div class="flex items-center gap-3 text-sm">
            @if($businesses->previousPageUrl())
                <a class="px-3 py-2 rounded-lg border border-slate-200" href="{{ $businesses->previousPageUrl() }}">←</a>
            @endif
            @if($businesses->nextPageUrl())
                <a class="px-3 py-2 rounded-lg border border-slate-200" href="{{ $businesses->nextPageUrl() }}">→</a>
            @endif
        </div>
    </div>
</form>

<form id="single-delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function toggleAll(source) {
        document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = source.checked);
    }

    function deleteSingle(url) {
        if (!confirm('Usunąć wpis?')) return;
        const form = document.getElementById('single-delete-form');
        form.action = url;
        form.submit();
    }
</script>
@endpush
@endsection
