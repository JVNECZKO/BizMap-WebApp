@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Edycja</p>
        <h1 class="text-2xl font-bold text-slate-900">{{ $business->full_name }}</h1>
    </div>
    <a href="{{ route('admin.businesses.index') }}" class="text-sm text-sky-700">← Powrót</a>
</div>

<form method="POST" action="{{ route('admin.businesses.update', $business) }}" class="bg-white rounded-2xl shadow-panel border border-slate-100 p-6 grid md:grid-cols-2 gap-4">
    @csrf
    @method('PUT')
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Nazwa</label>
        <input type="text" name="full_name" value="{{ old('full_name', $business->full_name) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2" required>
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">NIP</label>
        <input type="text" name="nip" value="{{ old('nip', $business->nip) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">REGON</label>
        <input type="text" name="regon" value="{{ old('regon', $business->regon) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Status</label>
        <input type="text" name="status_dzialalnosci" value="{{ old('status_dzialalnosci', $business->status_dzialalnosci) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Kod pocztowy</label>
        <input type="text" name="kod_pocztowy" value="{{ old('kod_pocztowy', $business->kod_pocztowy) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Powiat</label>
        <input type="text" name="powiat" value="{{ old('powiat', $business->powiat) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Gmina</label>
        <input type="text" name="gmina" value="{{ old('gmina', $business->gmina) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Miejscowość</label>
        <input type="text" name="miejscowosc" value="{{ old('miejscowosc', $business->miejscowosc) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>
    <div>
        <label class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD główne</label>
        <input type="text" name="glowny_kod_pkd" value="{{ old('glowny_kod_pkd', $business->glowny_kod_pkd) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
    </div>

    <div class="md:col-span-2 flex justify-end pt-2">
        <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Zapisz</button>
    </div>
</form>
@endsection
