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

<div class="mt-8 bg-white shadow-panel rounded-2xl border border-slate-100 p-6 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Migracja danych</p>
            <h2 class="text-xl font-bold text-slate-900">Przenieś dane z SQLite do MySQL</h2>
            <p class="text-sm text-slate-600">Dodaj konfigurację docelowej bazy MySQL/MariaDB, uruchom migrację, obserwuj log w czasie rzeczywistym. Po zakończeniu dostaniesz komunikat o możliwości przełączenia bazy.</p>
        </div>
    </div>

    <form id="migration-form" class="grid md:grid-cols-2 gap-4">
        @csrf
        <input type="hidden" name="driver" value="mysql">
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Host</label>
            <input type="text" name="host" value="{{ $target['host'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Port</label>
            <input type="text" name="port" value="{{ $target['port'] ?? '3306' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Baza</label>
            <input type="text" name="database" value="{{ $target['database'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Użytkownik</label>
            <input type="text" name="username" value="{{ $target['username'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div>
            <label class="text-xs uppercase tracking-[0.2em] text-slate-500">Hasło</label>
            <input type="password" name="password" value="{{ $target['password'] ?? '' }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3">
        </div>
        <div class="md:col-span-2 flex items-center gap-3 pt-2 flex-wrap">
            <button type="button" id="migration-save" class="px-4 py-3 rounded-xl bg-slate-900 text-white text-sm">Zapisz bazę docelową</button>
            <button type="button" id="migration-clear" class="px-4 py-3 rounded-xl border border-slate-200 text-sm">Usuń konfigurację</button>
            <button type="button" id="migration-run" class="px-4 py-3 rounded-xl bg-emerald-600 text-white text-sm">Utwórz schemat i migruj dane</button>
            <button type="button" id="migration-direct" class="px-4 py-3 rounded-xl bg-indigo-600 text-white text-sm">Migracja bezpośrednia (1 klik)</button>
            <span id="migration-status" class="text-sm text-slate-600"></span>
        </div>
    </form>

    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
        <p class="text-sm font-semibold text-slate-800 mb-2">Log migracji</p>
        <pre id="migration-log" class="text-xs text-slate-700 whitespace-pre-wrap h-60 overflow-y-auto bg-white rounded-lg border border-slate-200 p-3">Oczekiwanie na uruchomienie...</pre>
    </div>
</div>

@push('scripts')
<script>
const saveBtn = document.getElementById('migration-save');
const clearBtn = document.getElementById('migration-clear');
const runBtn = document.getElementById('migration-run');
const directBtn = document.getElementById('migration-direct');
const logBox = document.getElementById('migration-log');
const statusText = document.getElementById('migration-status');

async function post(url, body = {}) {
    // próba POST, a jeśli 403, fallback do GET (WAF)
    let res = await fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
        credentials: 'same-origin',
        body: JSON.stringify(body),
    });
    if (res.status === 403) {
        res = await fetch(url, {
            method: 'GET',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            credentials: 'same-origin',
        });
    }
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        throw new Error(data.error || ('HTTP ' + res.status));
    }
    return data;
}

async function postWithRetry(url, body = {}, attempts = 5, baseDelay = 2000) {
    for (let i = 1; i <= attempts; i++) {
        try {
            return await post(url, body);
        } catch (e) {
            if (i === attempts) throw e;
            const wait = baseDelay * i;
            statusText.textContent = `${e.message || 'Błąd'} (próba ${i}/${attempts}), czekam ${wait/1000}s...`;
            await new Promise(r => setTimeout(r, wait));
        }
    }
}

saveBtn?.addEventListener('click', async () => {
    const form = document.getElementById('migration-form');
    const payload = Object.fromEntries(new FormData(form));
    try {
        statusText.textContent = 'Zapisuję...';
        await post('{{ route('admin.database.migration.save') }}', payload);
        statusText.textContent = 'Zapisano konfigurację docelowej bazy.';
    } catch(e) {
        statusText.textContent = e.message;
    }
});

clearBtn?.addEventListener('click', async () => {
    try {
        statusText.textContent = 'Usuwam konfigurację...';
        await post('{{ route('admin.database.migration.clear') }}');
        statusText.textContent = 'Usunięto.';
    } catch(e) {
        statusText.textContent = e.message;
    }
});

runBtn?.addEventListener('click', async () => {
    try {
        statusText.textContent = 'Start migracji...';
        logBox.textContent = 'Start...';
        let resp = await postWithRetry('{{ route('admin.database.migration.start') }}', {}, 5, 2000);
        if (resp.log) {
            logBox.textContent = (resp.log || []).join("\n");
        }
        statusText.textContent = 'Migracja krokowa trwa...';

        let keepGoing = true;
        let safety = 0;
        while (keepGoing && safety < 5000) {
            try {
                resp = await postWithRetry('{{ route('admin.database.migration.run') }}', {}, 5, 2000);
            } catch(e) {
                statusText.textContent = e.message || 'Błąd migracji.';
                break;
            }
            if (resp.log) {
                logBox.textContent = (logBox.textContent + "\n" + resp.log.join("\n")).trim();
            }
            if (resp.status === 'finished') {
                keepGoing = false;
                statusText.textContent = resp.message || 'Zakończono.';
                break;
            }
            safety++;
            await new Promise(r => setTimeout(r, 800));
        }
        if (safety >= 5000) {
            statusText.textContent = 'Przerwano z powodu limitu prób. Sprawdź log.';
        }
    } catch(e) {
        statusText.textContent = e.message;
    }
});

directBtn?.addEventListener('click', async () => {
    try {
        statusText.textContent = 'Migracja bezpośrednia...';
        logBox.textContent = 'Start...';
        const resp = await postWithRetry('{{ route('admin.database.migration.direct') }}', {}, 3, 4000);
        if (resp.log) {
            logBox.textContent = (resp.log || []).join("\n");
        }
        statusText.textContent = resp.message || 'Zakończono.';
    } catch(e) {
        statusText.textContent = e.message || 'Błąd migracji.';
    }
});
</script>
@endpush
@endsection
