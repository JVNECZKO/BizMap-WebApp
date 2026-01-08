@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">PKD</p>
        <h1 class="text-2xl font-bold text-slate-900">Operacje PKD</h1>
        <p class="text-sm text-slate-600 mt-1">Import kodów, normalizacja firm i przeliczenie popularności.</p>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-4">
    <div class="p-5 bg-white rounded-2xl shadow-panel border border-slate-100">
        <p class="text-sm font-semibold text-slate-800">Import kodów PKD</p>
        <p class="text-sm text-slate-600 mt-2">Wczytuje kody PKD 2007 i 2025 z plików JSON w <code>storage/app/pkd</code>.</p>
        <button onclick="runPkd('import')" class="mt-3 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold shadow-panel" id="btn-import">Importuj</button>
    </div>
    <div class="p-5 bg-white rounded-2xl shadow-panel border border-slate-100">
        <p class="text-sm font-semibold text-slate-800">Normalizacja kodów firm</p>
        <p class="text-sm text-slate-600 mt-2">Porządkuje kody PKD w firmach i odbudowuje pivot <code>business_pkd_codes</code>.</p>
        <button onclick="runPkd('normalize')" class="mt-3 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold shadow-panel" id="btn-normalize">Normalizuj</button>
    </div>
    <div class="p-5 bg-white rounded-2xl shadow-panel border border-slate-100">
        <p class="text-sm font-semibold text-slate-800">Przelicz popularne PKD</p>
        <p class="text-sm text-slate-600 mt-2">Aktualizuje tabelę <code>pkd_popularity</code> na podstawie powiązań firm.</p>
        <button onclick="runPkd('recount')" class="mt-3 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold shadow-panel" id="btn-recount">Przelicz</button>
    </div>
</div>

<div class="mt-6 p-5 bg-white rounded-2xl shadow-panel border border-slate-100">
    <p class="text-sm font-semibold text-slate-800 mb-2">Log</p>
    <pre id="pkd-log" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 h-64 overflow-auto text-slate-700">Oczekiwanie...</pre>
</div>

@push('scripts')
<script>
function appendLog(line) {
    const log = document.getElementById('pkd-log');
    log.textContent += line + "\n";
    log.scrollTop = log.scrollHeight;
}

async function runPkd(action) {
    const buttons = {
        import: document.getElementById('btn-import'),
        normalize: document.getElementById('btn-normalize'),
        recount: document.getElementById('btn-recount'),
    };
    const log = document.getElementById('pkd-log');
    const btn = buttons[action];
    if (!btn) return;
    btn.disabled = true;
    const original = btn.textContent;
    btn.textContent = 'Wykonywanie...';

    log.textContent = '';
    appendLog(`Uruchamiam: ${action}...`);
    try {
        appendLog('Wysyłam żądanie do serwera...');
        const res = await fetch(`{{ route('admin.pkd.index') }}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });
        appendLog(`Status HTTP: ${res.status}`);
        const data = await res.json();
        if (data.ok) {
            appendLog('Odpowiedź OK, log polecenia:');
            appendLog(data.output || 'Zakończono.');
        } else {
            appendLog(`Błąd: ${data.error || 'nieznany'}`);
        }
    } catch (e) {
        appendLog(`Błąd sieci/serwera: ${e}`);
    } finally {
        btn.disabled = false;
        btn.textContent = original;
    }
}
</script>
@endpush
@endsection
