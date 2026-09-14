<!DOCTYPE html>
<html lang="pt-AO">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Entrar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-text antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="bg-surface-card backdrop-blur-xl border border-glass-border rounded-xl shadow-card p-8">
                <div class="text-center mb-8">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue to-purple flex items-center justify-center shadow-lg shadow-blue/30 mx-auto mb-4">
                        <span class="text-white font-bold text-lg">S</span>
                    </div>
                    <h1 class="text-xl font-bold text-text tracking-tight">Backoffice</h1>
                    <p class="text-sm text-text-3 mt-1">System</p>
                </div>

            <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-text-2 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-text-2 mb-1.5">Palavra-passe</label>
                    <input id="password" type="password" name="password" required
                        class="w-full px-3 py-2.5 bg-black/3 border border-black/10 rounded-lg text-sm text-text placeholder:text-text-3 focus:outline-none focus:border-blue/50 focus:bg-blue/2 transition-all">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-blue text-white text-sm font-semibold hover:bg-blue/90 transition-colors shadow-sm">
                    Entrar
                </button>
            </form>
        </div>
    </div>
</body>
</html>
