@extends('layouts.public')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-12">
    <div class="mb-8">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">PKD {{ $pkdVersion }}</p>
        <h1 class="text-3xl font-bold text-slate-900">Wszystkie kody PKD</h1>
        <p class="text-slate-600 mt-2">Pełna lista kodów PKD ({{ $pkdVersion }}). Kliknij, aby skopiować kod.</p>
    </div>
    <div class="bg-white rounded-2xl shadow-card border border-slate-200 divide-y divide-slate-100">
        @foreach($codes as $code)
            <a class="px-4 py-3 flex items-start justify-between hover:bg-slate-50 cursor-pointer"
               href="{{ route('companies.index', ['pkd' => $code->code]) }}">
                <div>
                    <p class="font-semibold text-slate-900">{{ $code->code }}</p>
                    <p class="text-sm text-slate-600">{{ $code->name }}</p>
                </div>
                <span class="text-xs text-slate-400 uppercase tracking-[0.2em]">Szukaj</span>
            </a>
        @endforeach
    </div>
</div>
@endsection
