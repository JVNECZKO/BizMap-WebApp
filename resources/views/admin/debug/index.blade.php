@extends('layouts.admin')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Debugowanie</p>
        <h1 class="text-2xl font-bold text-slate-900">Logowanie zapytań SQL</h1>
        <p class="text-sm text-slate-600">Włącz, aby pod stopką każdej strony w panelu wyświetlać listę zapytań z czasem wykonania (jak w Presta). Widoczne tylko dla administratora.</p>
    </div>

    <div class="rounded-2xl bg-white shadow-panel border border-slate-200 p-6 space-y-6">
        <form method="POST" action="{{ route('admin.debug.update') }}" class="space-y-4">
            @csrf
            <div class="flex items-center gap-3">
                <input type="checkbox" id="sql_debug" name="sql_debug" value="1" {{ $sqlEnabled ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300">
                <label for="sql_debug" class="text-slate-800 font-semibold">Włącz podgląd zapytań SQL dla administratora</label>
            </div>
            <p class="text-sm text-slate-500">Po włączeniu, blok „SQL debug” pojawi się na dole każdej strony (HTML) w panelu oraz na froncie, ale tylko dla zalogowanego admina.</p>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel hover:bg-slate-800">Zapisz ustawienie</button>
            </div>
        </form>

        <hr class="border-slate-100">

        <form method="POST" action="{{ route('admin.debug.update') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="refresh_filters" value="1">
            <p class="text-slate-800 font-semibold">Snapshot filtrów</p>
            <p class="text-sm text-slate-500">Zapisz listy województw / powiatów / gmin / miejscowości / statusów do cache i ustawień, aby nie liczyć ich przy każdym wejściu.</p>
            <button type="submit" class="px-5 py-3 rounded-xl bg-slate-100 text-slate-800 border border-slate-200 hover:bg-slate-200">Odśwież listy filtrów</button>
        </form>

        <hr class="border-slate-100">

        <form method="POST" action="{{ route('admin.debug.update') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="refresh_locations" value="1">
            <p class="text-slate-800 font-semibold">Drzewo lokalizacji</p>
            <p class="text-sm text-slate-500">Przelicz powiązania województwo → powiat → gmina → miejscowość i zapisz w cache. Używaj po dużych aktualizacjach danych.</p>
            <button type="submit" class="px-5 py-3 rounded-xl bg-slate-100 text-slate-800 border border-slate-200 hover:bg-slate-200">Odbuduj drzewo lokalizacji</button>
        </form>
    </div>
</div>
@endsection
