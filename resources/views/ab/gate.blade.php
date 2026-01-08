<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sprawdzenie</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    body{margin:0;font-family:system-ui;background:#0b0d12;color:#e7e9ee}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
    .card{max-width:520px;width:100%;background:#121622;border:1px solid #232a3b;border-radius:16px;padding:22px}
    .title{font-size:20px;font-weight:700;margin:0 0 8px}
    .sub{opacity:.85;line-height:1.4;margin:0 0 14px}
    .hint{opacity:.75;font-size:13px}
    .spinner{width:22px;height:22px;border:3px solid #2b3550;border-top-color:#8aa4ff;border-radius:50%;animation:spin 1s linear infinite;display:inline-block;vertical-align:middle;margin-right:10px}
    @keyframes spin{to{transform:rotate(360deg)}}
    .row{display:flex;align-items:center;margin-top:14px}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <p class="title"><i class="fa-solid fa-ban" style="margin-right:8px;color:#ef4444;"></i>Dostęp wstrzymany</p>
      <p class="sub" id="msg">
        Wykryliśmy aktywne oprogramowanie blokujące reklamy, które uniemożliwia prawidłowe działanie tego serwisu.
        Nasz serwis jest bezpłatny dla użytkowników i jego rozwój, utrzymanie infrastruktury oraz tworzenie treści
        są finansowane z reklam. Staramy się, aby były one nienachalne, bez dźwięku, wyskakujących okien
        oraz agresywnego śledzenia.
      </p>

      <div class="sub" style="margin-top:12px;">
        Aby kontynuować korzystanie z serwisu, prosimy o wyłączenie blokera reklam wyłącznie dla tej strony.
      </div>

      <div style="margin-top:16px; line-height:1.5; font-size:14px;">
        <strong><i class="fa-solid fa-shield-halved" style="margin-right:6px;color:#8aa4ff;"></i>Jak wyłączyć AdBlock / AdBlock Plus</strong>
        <ol style="padding-left:18px; margin:6px 0 14px 0;">
          <li>Kliknij ikonę rozszerzenia AdBlock / AdBlock Plus w pasku przeglądarki.</li>
          <li>Wybierz opcję „Nie uruchamiaj na stronach w tej domenie” lub „Wyłącz na tej stronie”.</li>
          <li>Odśwież stronę po zapisaniu zmian.</li>
        </ol>

        <strong><i class="fa-solid fa-toggle-off" style="margin-right:6px;color:#8aa4ff;"></i>Jak wyłączyć uBlock Origin</strong>
        <ol style="padding-left:18px; margin:6px 0 14px 0;">
          <li>Kliknij ikonę uBlock Origin obok paska adresu.</li>
          <li>Kliknij niebieską ikonę zasilania (⏻), aby wyłączyć blokowanie dla tej strony.</li>
          <li>Odśwież stronę.</li>
        </ol>

        <strong><i class="fa-solid fa-list-check" style="margin-right:6px;color:#8aa4ff;"></i>Inne blokery reklam</strong>
        <p style="margin:6px 0 12px 0;">
          Jeżeli korzystasz z innego narzędzia blokującego reklamy, sprawdź jego ustawienia i dodaj tę domenę
          do listy wyjątków (whitelist), a następnie odśwież stronę.
        </p>

        <p style="margin:6px 0 0 0;">
          Po wyłączeniu blokera reklam i odświeżeniu strony dostęp zostanie automatycznie przywrócony.
          Dziękujemy za zrozumienie i wsparcie — to dzięki temu serwis może dalej działać i rozwijać się.
        </p>
      </div>

      <div class="row" style="margin-top:18px;">
        <span class="spinner"></span>
        <span class="hint">Sprawdzam ponownie…</span>
      </div>

      <div id="ab-bait" class="ad ads ad-banner adsbox adunit"
           style="position:absolute;left:-9999px;top:-9999px;height:10px;width:10px;"></div>

      <script src="/ads.js" async></script>

      <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const nextUrl = @json($next);

        function sleep(ms){ return new Promise(r=>setTimeout(r, ms)); }

        async function fetchJson(url, body = {}) {
          const res = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify(body),
          });
          return res.json().catch(()=>({}));
        }

        function isBaitVisible() {
          const bait = document.getElementById('ab-bait');
          if (!bait) return false;
          const st = getComputedStyle(bait);
          const hidden = st.display === 'none' || st.visibility === 'hidden';
          const zero = bait.offsetHeight === 0 || bait.offsetWidth === 0;
          return !(hidden || zero);
        }

        async function ping(url, timeout = 1800) {
          const ctrl = new AbortController();
          const to = setTimeout(() => ctrl.abort(), timeout);
          try {
            const res = await fetch(url, { method:'GET', cache:'no-store', credentials:'same-origin', signal: ctrl.signal, mode: 'no-cors' });
            clearTimeout(to);
            return true;
          } catch(e) {
            clearTimeout(to);
            return false;
          }
        }

        async function isBlocked() {
          const baitOk = isBaitVisible();
          const scriptLoaded = window.__ads_script_loaded === true || [...document.scripts].some(s => (s.src || '').includes('/ads.js'));
          const localPing = await ping('/ads.js?cb=' + Date.now());
          const googlePing = await ping('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?cb=' + Date.now());

          // PASS only if all signals are good
          return !(baitOk && scriptLoaded && localPing && googlePing);
        }

        async function passAndGo() {
          await fetchJson('/ab/pass');
          window.location.replace(nextUrl);
        }

        async function loop() {
          const msg = document.getElementById('msg');

          for (;;) {
            const blocked = await isBlocked();

            if (!blocked) {
              msg.textContent = "OK. Wpuszczam cię…";
              await passAndGo();
              return;
            } else {
              msg.textContent = "AdBlock wykryty. Wyłącz go dla tej strony.";
            }

            await sleep(1200);
          }
        }

        loop();
      </script>
    </div>
  </div>
</body>
</html>
