<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wyłącz blokowanie reklam</title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#0f172a; color:white; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .box { max-width: 520px; padding: 32px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; }
        button { margin-top:16px; padding:12px 18px; border:none; border-radius:10px; background:white; color:#0f172a; font-weight:700; cursor:pointer; }
        ul { margin: 8px 0 0 18px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Wyłącz blokowanie reklam</h1>
        <p>BizMap utrzymuje się z reklam. Aby skorzystać z serwisu, wyłącz blokowanie reklam (także na poziomie DNS), a następnie kliknij „Sprawdź ponownie”.</p>
        <p><strong>Jak wyłączyć blokowanie reklam?</strong></p>
        <ul>
            <li>Dodaj BizMap do wyjątków w wtyczce blokującej.</li>
            <li>Wyłącz filtr blokowania reklam dla tej domeny.</li>
            <li>Jeśli używasz filtrów DNS (np. Pi-hole), dodaj domenę do whitelisty.</li>
        </ul>
        <button id="retry">Sprawdź ponownie</button>
    </div>
    <script>
        (function() {
            const btn = document.getElementById('retry');
            const csrf = '{{ csrf_token() }}';
            const redirectTo = @json($redirect ?? route('landing'));

            function checkBait() {
                return new Promise((resolve) => {
                    const bait = document.createElement('div');
                    bait.className = 'adsbox pub_300x250 ad-banner adbox adsbygoogle';
                    bait.style.cssText = 'position:absolute; left:-9999px; height:90px; width:120px;';
                    document.body.appendChild(bait);
                    setTimeout(() => {
                        const style = getComputedStyle(bait);
                        const hidden = bait.offsetHeight === 0 || bait.clientHeight === 0 || style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0';
                        bait.remove();
                        resolve(!hidden);
                    }, 250);
                });
            }

            function checkScript() {
                return new Promise((resolve) => {
                    const script = document.createElement('script');
                    script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
                    script.async = true;
                    const timer = setTimeout(() => resolve(false), 2500);
                    script.onload = () => { clearTimeout(timer); resolve(true); };
                    script.onerror = () => { clearTimeout(timer); resolve(false); };
                    document.head.appendChild(script);
                });
            }

            async function setCookieAndGo() {
                btn.disabled = true;
                btn.textContent = 'Sprawdzanie...';
                const [baitOk, scriptOk] = await Promise.all([checkBait(), checkScript()]);
                if (baitOk && scriptOk) {
                    try {
                        await fetch('{{ route('adblock.allow') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        });
                    } catch (e) {
                        // ignore
                    }
                    window.location.href = redirectTo;
                } else {
                    btn.disabled = false;
                    btn.textContent = 'Sprawdź ponownie';
                    alert('Wykryto aktywne blokowanie reklam. Wyłącz adblock i spróbuj ponownie.');
                }
            }

            btn.addEventListener('click', setCookieAndGo);
        })();
    </script>
</body>
</html>
