<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $logoPath = \App\Models\Setting::get('branding.logo');
        $faviconPath = \App\Models\Setting::get('branding.favicon');
    @endphp
    <title>Panel admin • {{ config('app.name') }}</title>
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "uyq6ocqveg");
    </script>
    @if($faviconPath)
        <link rel="icon" href="{{ asset('storage/' . $faviconPath) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Space Grotesk', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        primary: '#0F172A',
                        accent: '#0EA5E9',
                        soft: '#F8FAFC',
                    },
                    boxShadow: {
                        panel: '0 18px 40px rgba(15,23,42,0.12)',
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #f7fafc; }
    </style>
</head>
<body class="font-sans text-slate-900">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white border-r border-slate-200 shadow-panel hidden lg:block">
            <div class="px-6 py-6 border-b border-slate-100">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">BizMap</p>
                <p class="text-lg font-semibold text-slate-800">Panel administracyjny</p>
            </div>
            <nav class="px-4 py-6 space-y-1 text-sm">
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.dashboard') }}">Pulpit</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.businesses.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.businesses.index') }}">Firmy</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.imports.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.imports.index') }}">Importy</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.seo.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.seo.index') }}">SEO i nawigacja</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.branding.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.branding.index') }}">Branding</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.contact.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.contact.index') }}">Kontakt</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.database.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.database.index') }}">Baza danych</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.locations.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.locations.index') }}">Powiązania lokalizacji</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.ads.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.ads.index') }}">Reklamy</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.pkd.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.pkd.index') }}">PKD</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.taxonomy.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.taxonomy.index') }}">Branże PKD</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.account.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.account.edit') }}">Konto</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.sitemap.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.sitemap.index') }}">Sitemapa</a>
                <a class="block px-4 py-3 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.debug.*') ? 'bg-slate-100 font-semibold' : '' }}" href="{{ route('admin.debug.index') }}">Debugowanie</a>
            </nav>
        </aside>
        <div class="flex-1">
            <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-slate-900 to-slate-700"></div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">BizMap</p>
                            <p class="text-sm text-slate-700">{{ config('bizmap.admin_prefix') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="px-4 py-2 rounded-lg border border-slate-200 hover:border-slate-300">Wyloguj</button>
                        </form>
                        <a href="{{ route('admin.account.edit') }}" class="px-4 py-2 rounded-lg border border-slate-200 hover:border-slate-300">Konto</a>
                    </div>
                </div>
            </header>
            <main class="max-w-7xl mx-auto px-6 py-8">
                @if(session('status'))
                    <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
