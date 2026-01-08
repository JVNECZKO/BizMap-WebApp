<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie • BizMap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white shadow-2xl rounded-2xl w-full max-w-md p-8 border border-slate-100">
        <div class="flex items-center gap-3 mb-6">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-sky-400 to-blue-600 shadow-lg"></div>
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">BizMap</p>
                <p class="text-xl font-semibold text-slate-900">Panel administracyjny</p>
            </div>
        </div>
        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
                {{ $errors->first() }}
            </div>
        @endif
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-sm text-slate-600">Email</label>
                <input type="email" name="email" value="{{ old('email', 'kontakt@dropdigital.pl') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-sky-400 focus:ring-2 focus:ring-sky-100" required>
            </div>
            <div>
                <label class="text-sm text-slate-600">Hasło</label>
                <input type="password" name="password" value="Admin123" class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-3 focus:border-sky-400 focus:ring-2 focus:ring-sky-100" required>
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-slate-300">
                    <span class="text-slate-600">Zapamiętaj mnie</span>
                </label>
                <a href="{{ route('landing') }}" class="text-sky-600 hover:text-sky-800">Powrót</a>
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-slate-900 text-white font-semibold shadow-lg hover:-translate-y-0.5 transition">Zaloguj</button>
        </form>
    </div>
</body>
</html>
