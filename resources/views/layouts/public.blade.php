<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'Ogólnopolski rejestr firm' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? '' }}">
    @php
        $logoPath = \App\Models\Setting::get('branding.logo');
        $faviconPath = \App\Models\Setting::get('branding.favicon');
    @endphp
    <title>{{ $metaTitle ?? config('app.name') }}</title>
    @if($faviconPath)
        <link rel="icon" href="{{ asset('storage/' . $faviconPath) }}">
    @endif
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
    @stack('scripts')
</body>
</html>
