@extends('layouts.public')

@section('content')
<div class="bg-gradient-to-b from-slate-50 via-white to-slate-50">
    <div class="max-w-5xl mx-auto px-6 py-12 space-y-10">
        <section class="bg-white rounded-3xl shadow-card border border-slate-100 p-8 md:p-10 space-y-3">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Polityka prywatności</p>
            <h1 class="text-4xl font-extrabold text-slate-900">Polityka prywatności BizMap</h1>
            <p class="text-lg text-slate-700 leading-relaxed">Niniejszy dokument określa zasady przetwarzania danych osobowych i korzystania z serwisu BizMap. Szanujemy prywatność użytkowników i dbamy o zgodność z obowiązującymi przepisami prawa polskiego oraz unijnego, w szczególności RODO.</p>
        </section>

        <section class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
            <h2 class="text-2xl font-bold text-slate-900">Administrator danych</h2>
            <p class="text-slate-700 leading-relaxed">Administratorem danych osobowych jest: DropDigital Łukasz Janeczko.</p>
            <ul class="text-slate-700 leading-relaxed list-disc list-inside space-y-1">
                <li>Firma: DropDigital Łukasz Janeczko</li>
                <li>Imię i nazwisko: Łukasz Janeczko</li>
                <li>NIP: 9222883602</li>
                <li>REGON: 524965915</li>
                <li>Adres stałego miejsca wykonywania działalności: woj. lubelskie, pow. zamojski, gm. Łabunie, miejsc. Łabunie-Reforma 39, 22-437</li>
                <li>Adres do doręczeń: Łukasz Janeczko, Łabunie-Reforma 39, 22-437</li>
                <li>Adres do doręczeń elektronicznych: AE:PL-89630-23482-FGHJB-25</li>
                <li>Kontakt: kontakt@biz-map.pl, tel. 572 651 439</li>
            </ul>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Zakres przetwarzania</h2>
                <p class="text-slate-700 leading-relaxed">Przetwarzamy dane podane dobrowolnie przez użytkowników (np. imię, nazwisko, adres e-mail, treść wiadomości) w celu obsługi korespondencji i udzielenia odpowiedzi na pytania dotyczące danych prezentowanych w serwisie BizMap.</p>
                <p class="text-slate-700 leading-relaxed">Dane prezentowane w serwisie pochodzą z publicznego rejestru CEIDG i są przetwarzane w celu ich udostępniania w formie informacyjnej.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Podstawa prawna</h2>
                <p class="text-slate-700 leading-relaxed">Podstawą prawną przetwarzania danych podawanych w formularzu kontaktowym jest zgoda użytkownika oraz prawnie uzasadniony interes administratora polegający na prowadzeniu korespondencji i obsłudze zapytań (art. 6 ust. 1 lit. a i f RODO).</p>
                <p class="text-slate-700 leading-relaxed">W przypadku danych pochodzących z CEIDG podstawą jest publiczny charakter rejestru i przepisy regulujące udostępnianie informacji o działalności gospodarczej.</p>
            </div>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Cele przetwarzania</h2>
                <ul class="text-slate-700 leading-relaxed list-disc list-inside space-y-1">
                    <li>Udzielanie odpowiedzi na zapytania przesłane przez formularz kontaktowy.</li>
                    <li>Obsługa zgłoszeń dotyczących danych prezentowanych w serwisie.</li>
                    <li>Prezentowanie informacji z rejestru CEIDG w celach informacyjnych.</li>
                </ul>
            </div>
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Okres przechowywania</h2>
                <p class="text-slate-700 leading-relaxed">Dane z formularza kontaktowego są przechowywane przez okres niezbędny do obsługi korespondencji i realizacji zgłoszenia, a następnie mogą być archiwizowane przez czas wymagany przepisami prawa lub do czasu przedawnienia ewentualnych roszczeń.</p>
                <p class="text-slate-700 leading-relaxed">Dane pochodzące z CEIDG są przechowywane zgodnie z ich publicznym charakterem.</p>
            </div>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Prawa osób, których dane dotyczą</h2>
                <p class="text-slate-700 leading-relaxed">Użytkownikom przysługują prawa wynikające z RODO, w szczególności:</p>
                <ul class="text-slate-700 leading-relaxed list-disc list-inside space-y-1">
                    <li>prawo dostępu do danych,</li>
                    <li>prawo do sprostowania,</li>
                    <li>prawo do usunięcia („prawo do bycia zapomnianym”),</li>
                    <li>prawo do ograniczenia przetwarzania,</li>
                    <li>prawo do przenoszenia danych,</li>
                    <li>prawo do sprzeciwu wobec przetwarzania.</li>
                </ul>
                <p class="text-slate-700 leading-relaxed">Z uprawnień można skorzystać, kontaktując się z administratorem pod adresem: kontakt@biz-map.pl.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Odbiorcy danych</h2>
                <p class="text-slate-700 leading-relaxed">Dane mogą być powierzane upoważnionym podmiotom wspierającym obsługę korespondencji lub świadczenie usług na rzecz administratora (np. obsługa poczty elektronicznej), wyłącznie w zakresie niezbędnym do realizacji celów wskazanych w tej polityce.</p>
                <p class="text-slate-700 leading-relaxed">Dane publiczne pochodzące z CEIDG pozostają danymi publicznymi zgodnie z obowiązującymi przepisami.</p>
            </div>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Bezpieczeństwo danych</h2>
                <p class="text-slate-700 leading-relaxed">Administrator stosuje środki organizacyjne i administracyjne mające na celu ochronę danych osobowych przed dostępem osób nieuprawnionych oraz innymi przypadkami ujawnienia zgodnie z obowiązującymi przepisami.</p>
            </div>
            <div class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Kontakt w sprawach prywatności</h2>
                <p class="text-slate-700 leading-relaxed">W sprawach związanych z danymi osobowymi prosimy o kontakt pod adresem: kontakt@biz-map.pl lub telefonicznie: 572 651 439.</p>
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-card border border-slate-100 p-6 space-y-2">
            <h2 class="text-2xl font-bold text-slate-900">Źródło danych</h2>
            <p class="text-slate-700 leading-relaxed">BizMap prezentuje informacje pochodzące z Centralnej Ewidencji i Informacji o Działalności Gospodarczej (CEIDG). Serwis nie jest powiązany z administracją publiczną i nie modyfikuje oficjalnych wpisów.</p>
        </section>
    </div>
</div>
@endsection
