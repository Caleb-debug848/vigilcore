<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion — VigilCore OPS-01</title>

    <!-- Favicon Officiel VigilCore -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=3">
    <link rel="alternate icon" type="image/png" href="{{ asset('images/logo.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v=3">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=3">

    <!-- Styles & Scripts Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] dark:bg-[#090d16] text-slate-900 dark:text-slate-100 font-sans antialiased min-h-screen flex items-center justify-center p-4 selection:bg-[#0020B2] selection:text-white transition-colors duration-200">

    <!-- ==================================================== -->
    <!-- CARTE D'AUTHENTIFICATION ÉPURÉE (STYLE STRIPE/LINEAR) -->
    <!-- ==================================================== -->
    <div class="w-full max-w-[420px] bg-white dark:bg-[#111827] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none p-6 sm:p-8 space-y-6"
         x-data="{ showPassword: false }">

        <!-- En-tête Logo & Marque -->
        <div class="flex flex-col items-center text-center space-y-2.5">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.svg') }}" alt="VigilCore Logo" class="h-10 w-10 object-contain">
                <div class="text-left">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xl tracking-tight text-slate-900 dark:text-white">VigilCore</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-blue-50 dark:bg-blue-950/80 text-[#0020B2] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            OPS-01
                        </span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Système opérationnel"></span>
                    </div>
                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">Incident & SLA Console</p>
                </div>
            </div>
        </div>

        <!-- Notification de Statut de Session -->
        @if (session('status'))
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-mono">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulaire de Connexion -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Identifiant / Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('Email professionnel') }}
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="nom@vigilcore.internal"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0c101a] border @error('email') border-red-500 text-red-600 dark:text-red-400 @else border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-slate-100 @enderror text-xs font-mono placeholder-slate-400 focus:outline-none focus:border-[#0020B2] focus:ring-2 focus:ring-[#0020B2]/20 transition">
                @error('email')
                    <p class="text-[11px] text-red-600 dark:text-red-400 font-mono mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Mot de Passe avec Toggle Oeil -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        {{ __('Mot de passe') }}
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[11px] font-mono text-[#0020B2] dark:text-blue-400 hover:underline">
                            {{ __('Oublié ?') }}
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" 
                           :type="showPassword ? 'text' : 'password'" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••••••"
                           class="w-full px-3.5 pr-10 py-2.5 rounded-xl bg-slate-50 dark:bg-[#0c101a] border @error('password') border-red-500 text-red-600 dark:text-red-400 @else border-slate-200 dark:border-slate-700/80 text-slate-900 dark:text-slate-100 @enderror text-xs font-mono placeholder-slate-400 focus:outline-none focus:border-[#0020B2] focus:ring-2 focus:ring-[#0020B2]/20 transition">
                    
                    <button type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-[11px] text-red-600 dark:text-red-400 font-mono mt-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Case Maintenir la session (Remember Me) -->
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember" 
                           class="h-4 w-4 rounded bg-slate-100 dark:bg-[#0c101a] border-slate-300 dark:border-slate-700 text-[#0020B2] focus:ring-[#0020B2] cursor-pointer">
                    <span class="text-xs text-slate-600 dark:text-slate-400">
                        {{ __('Maintenir la session active') }}
                    </span>
                </label>
            </div>

            <!-- Bouton de Connexion Plein Bleu Royal -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl text-white font-semibold text-xs tracking-wide bg-[#0020B2] hover:bg-[#001ca0] active:scale-[0.99] shadow-sm shadow-[#0020B2]/30 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>{{ __('Se Connecter') }}</span>
                </button>
            </div>
        </form>

        <!-- Badge de Sécurité & Confidentialité -->
        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 text-center flex items-center justify-center gap-1.5 text-[11px] font-mono text-slate-400">
            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
            <span>{{ __('Connexion chiffrée SSL / TLS — srv901529') }}</span>
        </div>

    </div>

</body>
</html>
