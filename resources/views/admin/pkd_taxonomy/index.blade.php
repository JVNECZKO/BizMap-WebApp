@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Branże PKD</p>
        <h1 class="text-2xl font-bold text-slate-900">Mapowanie PKD → grupy/podgrupy</h1>
        <p class="text-sm text-slate-600">Importuj CSV (aleo_grupa, aleo_podgrupa, pkd_primary, pkd_secondary) lub edytuj ręcznie.</p>
    </div>
</div>

@if(session('status'))
    <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-xl bg-rose-50 text-rose-800 border border-rose-200">{{ $errors->first() }}</div>
@endif

<div class="bg-white shadow-panel rounded-2xl border border-slate-100 p-6 space-y-4 mb-6">
    <form action="{{ route('admin.taxonomy.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row md:items-center gap-3">
        @csrf
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Plik CSV</label>
            <input type="file" name="file" accept=".csv" required class="block mt-1 text-sm">
        </div>
        <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Importuj CSV (nadpisze)</button>
    </form>
    <form action="{{ route('admin.taxonomy.destroyAll') }}" method="POST" onsubmit="return confirm('Usunąć wszystkie rekordy?')" class="inline-block">
        @csrf @method('DELETE')
        <button class="px-4 py-2 rounded-lg border border-rose-200 text-rose-700 text-sm">Usuń wszystkie</button>
    </form>
    <a href="/storage/mapping/aleo_pkd_hybryda_pkd2025.csv" target="_blank" class="text-sky-700 text-sm underline">Podgląd pliku źródłowego</a>
</div>

<div class="bg-white shadow-panel rounded-2xl border border-slate-100 p-6">
    <p class="text-sm text-slate-700 mb-3">Rekordy: {{ $items->total() }}</p>
    <div class="space-y-6">
        @foreach($items as $item)
            <form action="{{ route('admin.taxonomy.update', $item) }}" method="POST" class="grid md:grid-cols-6 gap-3 items-end border-b border-slate-100 pb-4">
                @csrf @method('PUT')
                <div>
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Grupa</label>
                    <input type="text" name="group_name" value="{{ $item->group_name }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Podgrupa</label>
                    <input type="text" name="subgroup_name" value="{{ $item->subgroup_name }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD główny</label>
                    <input type="text" name="primary_code" value="{{ $item->primary_code }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD dodatkowe (|)</label>
                    <input type="text" name="secondary_codes" value="{{ $item->secondary_codes ? implode('|', $item->secondary_codes) : '' }}" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                </div>
                <div>
                    <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Zapisz</button>
                </div>
            </form>
        @endforeach
    </div>
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
