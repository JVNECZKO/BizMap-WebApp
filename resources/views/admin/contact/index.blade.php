@extends('layouts.admin')

@section('content')
<div class="max-w-6xl">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Kontakt</p>
        <h1 class="text-2xl font-bold text-slate-900">Dane kontaktowe i formularz</h1>
    </div>
    <div class="grid md:grid-cols-2 gap-6">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-panel p-6">
            <form method="POST" action="{{ route('admin.contact.update') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-slate-800">Email odbiorcy</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $phone) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Adres</label>
                    <input type="text" name="address" value="{{ old('address', $address) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Nagłówek na stronie</label>
                    <input type="text" name="headline" value="{{ old('headline', $headline) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Opis wprowadzający</label>
                    <textarea name="intro" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">{{ old('intro', $intro) }}</textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-800">Komunikat po wysłaniu</label>
                    <input type="text" name="success" value="{{ old('success', $success) }}" class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-2">
                </div>
                <button class="px-5 py-3 rounded-xl bg-slate-900 text-white shadow-panel hover:bg-slate-800">Zapisz</button>
            </form>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 shadow-panel p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-3">Wiadomości z formularza</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-[0.2em]">
                        <tr>
                            <th class="px-3 py-2 text-left">Nadawca</th>
                            <th class="px-3 py-2 text-left">Email</th>
                            <th class="px-3 py-2 text-left">Treść</th>
                            <th class="px-3 py-2 text-left">Data</th>
                            <th class="px-3 py-2 text-left"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($messages as $msg)
                            <tr>
                                <td class="px-3 py-2 font-semibold text-slate-900">{{ $msg->name }}</td>
                                <td class="px-3 py-2 text-slate-700">{{ $msg->email }}</td>
                                <td class="px-3 py-2 text-slate-700">{{ \Illuminate\Support\Str::limit($msg->message, 120) }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2"><a class="text-sky-700" href="{{ route('admin.contact.show', $msg) }}">Korespondencja</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-3 py-3 text-slate-500">Brak wiadomości.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
