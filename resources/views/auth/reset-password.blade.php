<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('vigilcore_theme') === 'dark' || (!('vigilcore_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="if (darkMode) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark');"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Nouveau Mot de Passe — VigilCore Platform</title>

    <!-- Favicon Officiel VigilCore -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=4">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=4">

    <!-- Styles & Scripts Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] dark:bg-[#090d16] text-slate-900 dark:text-slate-100 font-sans antialiased min-h-screen flex items-center justify-center p-4 selection:bg-[#0020B2] selection:text-white transition-colors duration-200">

    <!-- ==================================================== -->
    <!-- CARTE DE DÉFINITION DE NOUVEAU MOT DE PASSE          -->
    <!-- ==================================================== -->
    <div class="w-full max-w-[440px] bg-white dark:bg-[#111827] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none p-6 sm:p-8 space-y-5">

        <!-- En-tête Logo & Marque -->
        <div class="flex flex-col items-center text-center space-y-2.5">
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.svg') }}" alt="VigilCore Logo" class="h-10 w-10 object-contain group-hover:scale-105 transition-transform">
                <div class="text-left">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-2xl tracking-tight text-slate-900 dark:text-white">
                            Vigil<span class="text-[#2563EB] dark:text-blue-400">Core</span>
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-blue-50 dark:bg-blue-950/80 text-[#0020B2] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            SECURITY
                        </span>
                    </div>
                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">Accès Sécurisé Supervision</p>
                </div>
            </a>
        </div>

        <!-- Titre et Explication en Français -->
        <div class="space-y-1 text-center">
            <h1 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">
                {{ __('Nouveau mot de passe') }}
            </h1>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ __('Définissez un mot de passe robuste respectant la politique de sécurité.') }}
            </p>
        </div>

        <!-- Formulaire de Réinitialisation -->
        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf

            <!-- Token de Réinitialisation -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address (Grisé et Verrouillé) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        {{ __('Adresse e-mail') }}
                    </label>
                    <span class="inline-flex items-center gap-1 text-[10px] font-mono text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded border border-emerald-200 dark:border-emerald-800">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>{{ __('Compte vérifié') }}</span>
                    </span>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email', $request->email) }}" 
                       readonly
                       tabindex="-1"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-100 dark:bg-[#151c2c] border border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 font-mono cursor-not-allowed select-none focus:outline-none transition">
                @error('email')
                    <p class="text-[11px] font-mono text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Nouveau mot de passe avec Toggle) -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('Nouveau mot de passe') }}
                </label>
                <div class="relative">
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autofocus
                           autocomplete="new-password"
                           placeholder="Min. 8 caractères"
                           class="w-full px-3.5 pr-11 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0020B2] dark:focus:ring-blue-500 focus:border-transparent transition">
                    
                    <button type="button" 
                            onclick="togglePasswordInput('password', this)"
                            aria-label="{{ __('Afficher ou masquer') }}"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none cursor-pointer transition">
                        <svg class="w-4 h-4 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="w-4 h-4 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-[11px] font-mono text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('Confirmer le mot de passe') }}
                </label>
                <div class="relative">
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Confirmez le mot de passe"
                           class="w-full px-3.5 pr-11 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0020B2] dark:focus:ring-blue-500 focus:border-transparent transition">
                    
                    <button type="button" 
                            onclick="togglePasswordInput('password_confirmation', this)"
                            aria-label="{{ __('Afficher ou masquer') }}"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none cursor-pointer transition">
                        <svg class="w-4 h-4 eye-open" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg class="w-4 h-4 eye-closed hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-[11px] font-mono text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bouton d'Enregistrement -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-[#0020B2] hover:bg-[#001ca0] text-white text-xs font-semibold shadow-xs hover:shadow-md transition duration-150 cursor-pointer flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ __('Mettre à jour le mot de passe') }}</span>
            </button>
        </form>

        <!-- Lien de Retour à la Connexion -->
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#0020B2] dark:text-blue-400 hover:underline">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Retour à la page de connexion') }}</span>
            </a>
        </div>

    </div>

    <script>
        function togglePasswordInput(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');
            if (eyeOpen && eyeClosed) {
                eyeOpen.classList.toggle('hidden', isPassword);
                eyeClosed.classList.toggle('hidden', !isPassword);
            }
        }
    </script>
</body>
</html>
