@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Baza danych</p>
        <h1 class="text-2xl font-bold text-slate-900">Konfiguracja połączenia</h1>
    </div>
</div>

<div class="bg-white shadow-panel rounded-2xl border border-slate-100 p-6 space-y-4">
    <form method="POST" action="{{ route('admin.database.update') }}" class="grid md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Driver</label>
            <select name="driver" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
                <option value="sqlite" @selected($driver === 'sqlite')>SQLite</option>
                <option value="mysql" @selected($driver === 'mysql')>MySQL / MariaDB</option>
            </select>
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Host</label>
            <input type="text" name="host" value="{{ old('host', $host) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Port</label>
            <input type="text" name="port" value="{{ old('port', $port) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Baza / ścieżka</label>
            <input type="text" name="database" value="{{ old('database', $database) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Użytkownik</label>
            <input type="text" name="username" value="{{ old('username', $username) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Hasło</label>
            <input type="password" name="password" value="{{ old('password', $password) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2">
            <button formaction="{{ route('admin.database.update') }}" class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel">Zapisz</button>
            <button formaction="{{ route('admin.database.test') }}" class="px-4 py-3 rounded-xl border border-slate-200 text-sm">Test połączenia</button>
            <button formaction="{{ route('admin.database.migrate') }}" class="px-4 py-3 rounded-xl bg-emerald-600 text-white text-sm">Migracja / aktualizacja schematu</button>
        </div>
    </form>
</div>
@endsection
