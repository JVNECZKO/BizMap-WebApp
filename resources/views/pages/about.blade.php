@extends('layouts.public')

@section('content')
<div class="bg-gradient-to-b from-slate-50 via-white to-slate-50">
    <div class="max-w-6xl mx-auto px-6 py-12 space-y-12">
        <section class="bg-white rounded-3xl shadow-card border border-slate-100 p-8 md:p-10 space-y-4">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">O nas</p>
            <h1 class="text-4xl font-extrabold text-slate-900">BizMap – ogólnopolski rejestr firm</h1>
            <p class="text-lg text-slate-700 leading-relaxed">BizMap to publiczny rejestr działalności gospodarczych oparty na danych CEIDG. Zebraliśmy informacje o firmach w jednym miejscu, aby były łatwe do przeglądania, porównywania i analizy. Serwis jest dostępny bez rejestracji – każdy może szybko sprawdzić interesujące go dane.</p>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-3">
                <h2 class="text-2xl font-bold text-slate-900">Dlaczego powstał BizMap?</h2>
                <p class="text-slate-700 leading-relaxed">Publiczne dane o firmach są dostępne, ale często trudno z nich korzystać na co dzień. BizMap porządkuje informacje i prezentuje je w przejrzystej formie, tak aby każdy mógł je wykorzystać w pracy biznesowej i analitycznej.</p>
                <p class="text-slate-700 leading-relaxed">Stawiamy na praktyczność: wsparcie decyzji, analiz rynkowych i weryfikacji partnerów. BizMap to neutralne narzędzie do pracy z informacją gospodarczą.</p>
            </div>
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-3">
                <h2 class="text-2xl font-bold text-slate-900">Jakie dane prezentujemy?</h2>
                <p class="text-slate-700 leading-relaxed">Pokazujemy dane identyfikacyjne firm, status prowadzonej działalności, kluczowe daty wpisu oraz klasyfikację branżową PKD (2007 i 2025). Znajdziesz tu także informacje o lokalizacji: województwo, powiat, gmina, miejscowość i adres – wszystko w spójnej, uporządkowanej formie.</p>
            </div>
        </section>

        <section class="space-y-4">
            <h2 class="text-3xl font-bold text-slate-900">Kto korzysta z BizMap?</h2>
            <div class="grid md:grid-cols-2 gap-5">
                <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                    <h3 class="text-xl font-semibold text-slate-900">Instytucje finansowe i ubezpieczeniowe</h3>
                    <p class="text-slate-700 leading-relaxed">Banki, firmy leasingowe i ubezpieczyciele weryfikują kontrahentów, status działalności oraz lokalizację. BizMap pomaga ograniczyć ryzyko i przyspieszyć decyzje biznesowe.</p>
                    <p class="text-slate-700 leading-relaxed">Aktualne wpisy ułatwiają potwierdzanie informacji przed podpisaniem umowy.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                    <h3 class="text-xl font-semibold text-slate-900">Zespoły sprzedaży i handlu B2B</h3>
                    <p class="text-slate-700 leading-relaxed">Działy sprzedaży wyszukują potencjalnych klientów według branży, regionu i typu działalności. BizMap wspiera planowanie działań i segmentację rynku.</p>
                    <p class="text-slate-700 leading-relaxed">Informacje o statusie i profilu firmy pomagają skupić się na obiecujących kontaktach.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                    <h3 class="text-xl font-semibold text-slate-900">Media, portale lokalne i branżowe</h3>
                    <p class="text-slate-700 leading-relaxed">Redakcje tworzą katalogi firm, profile przedsiębiorców i zestawienia oparte na wiarygodnych danych. BizMap wspiera przygotowanie rzetelnych materiałów o lokalnym biznesie.</p>
                    <p class="text-slate-700 leading-relaxed">Aktualność informacji pomaga utrzymać jakość publikacji.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                    <h3 class="text-xl font-semibold text-slate-900">Analitycy rynku i doradcy</h3>
                    <p class="text-slate-700 leading-relaxed">Specjaliści badają strukturę branżową, nasycenie rynku i trendy w sektorach oraz regionach. Dane w BizMap ułatwiają porównania i przygotowanie rekomendacji.</p>
                    <p class="text-slate-700 leading-relaxed">Przejrzyste informacje wspierają tworzenie raportów i analiz.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                    <h3 class="text-xl font-semibold text-slate-900">Przedsiębiorcy i osoby planujące działalność</h3>
                    <p class="text-slate-700 leading-relaxed">BizMap pozwala sprawdzić konkurencję, przeanalizować lokalny rynek i zweryfikować potencjalnych partnerów. To szybki wgląd w otoczenie biznesowe.</p>
                    <p class="text-slate-700 leading-relaxed">Rzetelne informacje ułatwiają planowanie i podejmowanie decyzji.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                    <h3 class="text-xl font-semibold text-slate-900">Organizacje i instytucje publiczne</h3>
                    <p class="text-slate-700 leading-relaxed">Samorządy i izby gospodarcze tworzą przeglądy lokalnych firm, przygotowują informacje dla przedsiębiorców i monitorują strukturę gospodarczą regionu.</p>
                    <p class="text-slate-700 leading-relaxed">Zebrane dane wspierają dialog z rynkiem i planowanie inicjatyw.</p>
                </div>
            </div>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Wiarygodność danych</h2>
                <p class="text-slate-700 leading-relaxed">BizMap opiera się na informacjach z CEIDG – oficjalnego rejestru działalności gospodarczych. Dane odzwierciedlają wpisy publiczne i mogą się zmieniać niezależnie od serwisu.</p>
                <p class="text-slate-700 leading-relaxed">Serwis ma charakter informacyjny i pomaga w dostępie do aktualnych informacji o firmach.</p>
            </div>
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Jak BizMap jest wykorzystywany w praktyce?</h2>
                <p class="text-slate-700 leading-relaxed">Użytkownicy sprawdzają kontrahentów przed podpisaniem umów, analizują rynek i konkurencję, tworzą lokalne przeglądy firm oraz materiały redakcyjne i analityczne.</p>
                <p class="text-slate-700 leading-relaxed">BizMap ułatwia wyszukiwanie branż, lokalizacji i statusów działalności, wspierając zarówno bieżącą pracę, jak i długoterminowe planowanie.</p>
            </div>
        </section>

        <section class="grid md:grid-cols-2 gap-6">
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Dostępność i przejrzystość</h2>
                <p class="text-slate-700 leading-relaxed">BizMap jest publicznie dostępny i nie wymaga zakładania konta. Informacje są przedstawione jasno i konsekwentnie, aby każdy mógł szybko znaleźć potrzebne dane.</p>
                <p class="text-slate-700 leading-relaxed">Przejrzystość opisów firm, kodów PKD i lokalizacji ułatwia porównywanie wpisów.</p>
            </div>
            <div class="p-6 rounded-2xl bg-white shadow-card border border-slate-100 space-y-2">
                <h2 class="text-2xl font-bold text-slate-900">Informacja prawna</h2>
                <p class="text-slate-700 leading-relaxed">Prezentowane dane mają charakter informacyjny. Użytkownicy podejmują decyzje biznesowe na własną odpowiedzialność. Źródłem informacji jest CEIDG. BizMap nie jest powiązany z administracją publiczną i pełni funkcję niezależnego serwisu informacyjnego.</p>
            </div>
        </section>
    </div>
</div>
@endsection
