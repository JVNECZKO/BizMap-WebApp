@extends('layouts.admin')

@section('content')
<div class="max-w-3xl">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Konto</p>
        <h1 class="text-2xl font-bold text-slate-900">Ustawienia konta administratora</h1>
    </div>
    @if(session('status'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="rounded-2xl bg-white border border-slate-200 shadow-panel p-6 space-y-4">
        <form method="POST" action="{{ route('admin.account.update') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm font-semibold text-slate-800">Nowe hasło</label>
                <input type="password" name="password" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2" placeholder="Pozostaw puste, aby nie zmieniać">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-800">Powtórz hasło</label>
                <input type="password" name="password_confirmation" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="two_factor_enabled" value="1" id="two_factor_enabled" {{ $user->two_factor_enabled ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300">
                <label for="two_factor_enabled" class="text-sm font-semibold text-slate-800">Włącz dodatkowe potwierdzenie e-mail (2FA)</label>
            </div>
            <p class="text-sm text-slate-500">Po włączeniu logowanie będzie wymagało kodu wysyłanego na e-mail.</p>
            <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel hover:bg-slate-800">Zapisz ustawienia</button>
        </form>
    </div>
</div>
@endsection
