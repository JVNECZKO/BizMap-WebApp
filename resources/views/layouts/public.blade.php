<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'Ogólnopolski rejestr firm' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://quge5.com/88/tag.min.js" data-zone="200151" async data-cfasync="false"></script>
    @php
        $logoPath = \App\Models\Setting::get('branding.logo');
        $faviconPath = \App\Models\Setting::get('branding.favicon');
    @endphp
    <title>{{ $metaTitle ?? config('app.name') }}</title>
    @if($faviconPath)
        <link rel="icon" href="{{ asset('storage/' . $faviconPath) }}">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        primary: '#0F172A',
                        accent: '#0EA5E9',
                        soft: '#F8FAFC',
                    },
                    boxShadow: {
                        card: '0 20px 50px rgba(15,23,42,0.08)',
                    }
                }
            }
        }
    </script>
    @php
        $adProviders = \App\Models\Setting::get('ads.providers', []);
    @endphp
    <style>
        body {
            background: radial-gradient(circle at 10% 20%, #e0f2fe 0, transparent 25%), radial-gradient(circle at 90% 10%, #e2e8f0 0, transparent 20%), #ffffff;
        }
        .glass {
            backdrop-filter: blur(8px);
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.6);
        }
    </style>
</head>
<body class="font-sans text-slate-900 antialiased">
    @if(is_array($adProviders))
        @foreach($adProviders as $provider)
            @if(($provider['enabled'] ?? false) && !empty($provider['code']))
                {!! $provider['code'] !!}
            @endif
        @endforeach
    @endif
    <div class="relative">
        <nav class="sticky top-0 z-40 glass shadow-card">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    @if($logoPath)
                        <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-12 object-contain">
                    @else
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 shadow-card"></div>
                    @endif
                </a>
                <div class="flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('landing') }}" class="text-slate-600 hover:text-slate-900 transition">Strona główna</a>
                    <a href="{{ route('companies.index') }}" class="text-slate-600 hover:text-slate-900 transition">Firmy</a>
                    <a href="{{ route('pkd.index') }}" class="text-slate-600 hover:text-slate-900 transition">PKD</a>
                    <a href="{{ route('about') }}" class="text-slate-600 hover:text-slate-900 transition">O nas</a>
                    <a href="{{ route('contact') }}" class="text-slate-600 hover:text-slate-900 transition">Kontakt</a>
                </div>
            </div>
        </nav>

        <main class="min-h-screen">
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white/90">
            <div class="max-w-6xl mx-auto px-6 py-10 grid md:grid-cols-3 gap-6">
                <div class="space-y-2">
                    @php
                        $logoPath = \App\Models\Setting::get('branding.logo');
                    @endphp
                    <div class="flex flex-col items-start gap-3">
                        @if($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="Logo" class="h-12 object-contain">
                        @else
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 shadow-card"></div>
                        @endif
                        <div class="text-slate-800">
                            <p class="text-sm text-slate-600">DropDigital Łukasz Janeczko</p>
                            <p class="text-sm text-slate-600">NIP: 9222883602</p>
                            <p class="text-sm text-slate-600">REGON: 524965915</p>
                        </div>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-slate-700 mb-3">Nawigacja</p>
                    <div class="space-y-2 text-sm text-slate-600">
                        <a class="block hover:text-slate-900" href="{{ route('landing') }}">Start</a>
                        <a class="block hover:text-slate-900" href="{{ route('companies.index') }}">Wyszukiwarka firm</a>
                        <a class="block hover:text-slate-900" href="{{ route('about') }}">O nas</a>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-slate-700 mb-3">Informacje prawne</p>
                    <div class="space-y-2 text-sm text-slate-600">
                        <a class="block hover:text-slate-900" href="{{ route('privacy') }}">Polityka prywatności</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <div id="ab-overlay" style="display:none;position:fixed;inset:0;background:rgba(10,12,18,.92);z-index:999999;color:#fff">
      <div style="max-width:620px;margin:12vh auto;padding:24px;background:#141a28;border:1px solid #2a3550;border-radius:16px;line-height:1.45;">
        <div style="font-size:20px;font-weight:800;margin-bottom:8px;display:flex;align-items:center;gap:8px">
          <i class="fa-solid fa-ban" style="color:#ef4444;"></i>
          <span>Dostęp wstrzymany</span>
        </div>
        <div style="opacity:.9;">
          <p style="margin:0 0 10px 0;">
            Wykryliśmy aktywne oprogramowanie blokujące reklamy, które uniemożliwia prawidłowe działanie tego serwisu.
            Nasz serwis jest bezpłatny dla użytkowników i jego rozwój finansowany jest z reklam – dbamy, by były one
            nienachalne, bez dźwięku, wyskakujących okien i agresywnego śledzenia.
          </p>
          <p style="margin:0 0 10px 0;">
            Aby kontynuować, wyłącz blokera reklam dla tej strony i odśwież stronę. Dostęp zostanie automatycznie przywrócony.
          </p>
          <p style="margin:0;">
            <strong>Podpowiedź:</strong> w AdBlock/AdBlock Plus wybierz „Nie uruchamiaj na stronach w tej domenie”,
            w uBlock Origin kliknij ikonę zasilania (⏻) dla tej strony, a następnie odśwież.
          </p>
        </div>
      </div>
    </div>

    <div id="ab-bait2" class="ad ads ad-banner adsbox adunit"
         style="position:absolute;left:-9999px;top:-9999px;height:10px;width:10px;"></div>
    <script src="/ads.js" async></script>

    @stack('scripts')
    <script>
    (() => {
      const overlay = document.getElementById('ab-overlay');
      const bait = document.getElementById('ab-bait2');
      const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

      function baitVisible(){
        if (!bait) return false;
        const st = getComputedStyle(bait);
        const hidden = st.display === 'none' || st.visibility === 'hidden';
        const zero = bait.offsetHeight === 0 || bait.offsetWidth === 0;
        return !(hidden || zero);
      }

      async function ping(url, timeout = 1800){
        const ctrl = new AbortController();
        const timer = setTimeout(() => ctrl.abort(), timeout);
        try {
          await fetch(url, {method:'GET', cache:'no-store', credentials:'same-origin', signal: ctrl.signal, mode:'no-cors'});
          clearTimeout(timer);
          return true;
        } catch(e) {
          clearTimeout(timer);
          return false;
        }
      }

      async function isBlocked(){
        const baitOk = baitVisible();
        const scriptLoaded = window.__ads_script_loaded === true || [...document.scripts].some(s => (s.src||'').includes('/ads.js'));
        const localPing = await ping('/ads.js?cb=' + Date.now());
        const googlePing = await ping('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?cb=' + Date.now());
        return !(baitOk && scriptLoaded && localPing && googlePing);
      }

      async function fail(){
        try {
          await fetch('/ab/fail', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf || ''},
            credentials: 'same-origin',
            body: '{}'
          });
        } catch(e) {}
        overlay.style.display = 'block';
        document.documentElement.style.overflow = 'hidden';
      }

      let tripped = false;

      async function check(){
        if (tripped) return;
        if (await isBlocked()){
          tripped = true;
          await fail();
        }
      }

      setInterval(check, 2500);
      window.addEventListener('focus', check);
      document.addEventListener('visibilitychange', () => { if (!document.hidden) check(); });
      check();
    })();
    </script>
</body>
</html>
