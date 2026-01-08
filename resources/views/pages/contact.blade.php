@extends('layouts.public')

@section('content')
<div class="bg-gradient-to-b from-slate-50 via-white to-slate-50">
    <div class="max-w-6xl mx-auto px-6 py-12 space-y-10">
        <section class="bg-white rounded-3xl shadow-card border border-slate-100 p-8 md:p-10 space-y-3">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Kontakt</p>
            <h1 class="text-4xl font-extrabold text-slate-900">Skontaktuj się z nami</h1>
            <p class="text-lg text-slate-700 leading-relaxed">BizMap to serwis informacyjny. Jeśli masz pytania dotyczące prezentowanych danych lub chcesz zgłosić uwagi merytoryczne, napisz do nas. Kontakt służy sprawom informacyjnym i weryfikacyjnym, a nie ofertom handlowym.</p>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-3">
                <h2 class="text-2xl font-bold text-slate-900">W jakich sprawach można się z nami skontaktować?</h2>
                <ul class="list-disc list-inside text-slate-700 space-y-2">
                    <li>Pytania dotyczące danych prezentowanych w serwisie BizMap.</li>
                    <li>Zgłoszenia nieprawidłowości lub brakujących informacji.</li>
                    <li>Zapytania związane z zakresem danych CEIDG i ich interpretacją.</li>
                    <li>Kwestie merytoryczne dotyczące korzystania z rejestru.</li>
                </ul>
            </div>
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-3">
                <h2 class="text-2xl font-bold text-slate-900">W jakich sprawach nie prowadzimy korespondencji?</h2>
                <ul class="list-disc list-inside text-slate-700 space-y-2">
                    <li>Oferty sprzedażowe i propozycje marketingowe.</li>
                    <li>Prośby o integracje techniczne lub obsługę reklamową.</li>
                    <li>Indywidualne żądania modyfikacji danych w rejestrze.</li>
                    <li>Zapytania niezwiązane z zakresem informacji prezentowanych w BizMap.</li>
                </ul>
            </div>
        </section>

        <section class="grid md:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2 md:col-span-2">
                <h2 class="text-2xl font-bold text-slate-900">Dane kontaktowe</h2>
                <p class="text-slate-700 leading-relaxed">Preferujemy kontakt pisemny. Odpowiadamy w możliwie krótkim czasie na wiadomości dotyczące zakresu danych i pytań merytorycznych.</p>
                <div class="mt-3 space-y-1">
                    <p class="text-sm text-slate-500">Adres e-mail</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $contactEmail }}</p>
                    @if($contactPhone)<p class="text-slate-700">{{ $contactPhone }}</p>@endif
                    @if($contactAddress)<p class="text-slate-700">{{ $contactAddress }}</p>@endif
                </div>
            </div>
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100">
                <h2 class="text-xl font-semibold text-slate-900">Informacja o charakterze zapytań</h2>
                <p class="text-slate-700 leading-relaxed">BizMap prezentuje publiczne informacje o firmach. Decyzje podejmowane na podstawie tych danych należą do użytkownika. Kontakt z nami nie stanowi potwierdzenia prawnego ani weryfikacji wpisów.</p>
            </div>
        </section>

        @if($contactSuccess)
            <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700">{{ $contactSuccess }}</div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="grid md:grid-cols-2 gap-6 items-start">
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Formularz kontaktowy</h2>
                <p class="text-slate-700 leading-relaxed">Wiadomości są analizowane ręcznie i służą sprawom merytorycznym.</p>
                <form method="POST" action="{{ route('contact.send') }}" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="text-sm font-semibold text-slate-800">Imię i nazwisko</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3" required>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-800">Adres e-mail</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3" required>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-800">Treść wiadomości</label>
                        <textarea name="message" rows="4" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3" required>{{ old('message') }}</textarea>
                    </div>
                    <button class="w-full px-5 py-3 rounded-xl bg-slate-900 text-white shadow-card hover:-translate-y-0.5 transition">Wyślij wiadomość</button>
                </form>
            </div>
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Źródło danych</h2>
                <p class="text-slate-700 leading-relaxed">Dane prezentowane w BizMap pochodzą z CEIDG. Serwis nie jest powiązany z administracją publiczną i nie modyfikuje oficjalnych wpisów.</p>
                <p class="text-slate-700 leading-relaxed">Kontakt służy zgłaszaniu uwag i pytań dotyczących prezentowanych informacji.</p>
            </div>
        </section>
    </div>
</div>
@endsection
