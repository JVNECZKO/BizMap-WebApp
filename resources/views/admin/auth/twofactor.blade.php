@extends('layouts.admin')

@section('content')
<div class="max-w-md mx-auto">
    @if(session('status'))
        <div class="mb-4 p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-800">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="rounded-2xl bg-white shadow-panel border border-slate-200 p-6 space-y-4">
        <h1 class="text-2xl font-bold text-slate-900">Wpisz kod 2FA</h1>
        <p class="text-slate-600 text-sm">Na Twój adres e-mail został wysłany kod logowania. Wpisz go, aby dokończyć logowanie.</p>
        <form method="POST" action="{{ route('admin.2fa.verify') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold text-slate-800">Kod</label>
                <input type="text" name="code" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3" required>
            </div>
            <button class="w-full px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel hover:bg-slate-800">Potwierdź</button>
        </form>
    </div>
</div>
@endsection
