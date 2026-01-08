@extends('layouts.admin')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Kontakt</p>
        <h1 class="text-2xl font-bold text-slate-900">Korespondencja</h1>
    </div>
    <div class="rounded-2xl bg-white border border-slate-200 shadow-panel p-6 space-y-4">
        <div class="flex items-center justify-between text-sm text-slate-600">
            <div>
                <p class="font-semibold text-slate-900">{{ $message->name }}</p>
                <p class="text-slate-700">{{ $message->email }}</p>
            </div>
            <p>{{ $message->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="border-t border-slate-100 pt-4">
            <p class="text-xs uppercase tracking-[0.2em] text-slate-500 mb-2">Treść wiadomości</p>
            <div class="whitespace-pre-line text-slate-800 leading-relaxed">{{ $message->message }}</div>
        </div>
        <div class="pt-4">
            <a href="{{ route('admin.contact.index') }}" class="text-sky-700 hover:text-sky-900">← Powrót do listy</a>
        </div>
    </div>
</div>
@endsection
