(function () {
    let baitOk = false;
    let scriptOk = false;
    const overlay = document.getElementById('adblock-overlay');
    const retryBtn = document.getElementById('adblock-retry');

    function showOverlay() {
        if (!overlay) return;
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function allowAccess() {
        if (overlay && baitOk && scriptOk) {
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Bait element check
    function baitCheck() {
        const bait = document.createElement('div');
        bait.className = 'adsbox pub_300x250 ad-banner adbox';
        bait.style.position = 'absolute';
        bait.style.left = '-9999px';
        bait.style.height = '90px';
        bait.style.width = '120px';
        document.body.appendChild(bait);
        window.setTimeout(() => {
            const hidden = bait.offsetHeight === 0 || bait.clientHeight === 0;
            bait.remove();
            if (hidden) {
                showOverlay();
            } else {
                baitOk = true;
                allowAccess();
            }
        }, 150);
    }

    // Try to load ads script and catch errors
    function scriptCheck() {
        const script = document.createElement('script');
        script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js';
        script.async = true;
        script.onerror = showOverlay;
        const timer = setTimeout(showOverlay, 2000);
        script.onload = () => {
            clearTimeout(timer);
            scriptOk = true;
            allowAccess();
        };
        document.head.appendChild(script);
    }

    function runChecks() {
        if (!overlay) return;
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        baitOk = false;
        scriptOk = false;
        baitCheck();
        scriptCheck();
    }

    document.addEventListener('DOMContentLoaded', runChecks);
    retryBtn?.addEventListener('click', runChecks);
})();
