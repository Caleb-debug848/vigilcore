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

    <title>Récupération d'Accès — VigilCore OPS-01</title>

    <!-- Favicon Officiel VigilCore -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=3">
    <link rel="alternate icon" type="image/png" href="{{ asset('images/logo.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}?v=3">
    <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=3">

    <!-- Styles & Scripts Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] dark:bg-[#090d16] text-slate-900 dark:text-slate-100 font-sans antialiased min-h-screen flex items-center justify-center p-4 selection:bg-[#0020B2] selection:text-white transition-colors duration-200" style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">

    <!-- ==================================================== -->
    <!-- CARTE DE RÉCUPÉRATION D'ACCÈS SÉCURISÉE (COMPACTE)   -->
    <!-- ==================================================== -->
    <div style="max-width: 440px; width: 100%; margin: 0 auto;"
         class="bg-white dark:bg-[#111827] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none p-6 sm:p-8 space-y-5">

        <!-- En-tête Logo & Marque -->
        <div class="flex flex-col items-center text-center space-y-2.5">
            <a href="/" class="flex items-center gap-2.5 group">
                <img src="{{ asset('images/logo.svg') }}" alt="VigilCore Logo" style="height: 40px; width: 40px;" class="object-contain group-hover:scale-105 transition-transform">
                <div class="text-left">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-xl tracking-tight text-slate-900 dark:text-white">VigilCore</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-blue-50 dark:bg-blue-950/80 text-[#0020B2] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            OPS-01
                        </span>
                    </div>
                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">Accès Sécurisé Supervision</p>
                </div>
            </a>
        </div>

        <!-- Titre et Explication en Français -->
        <div class="space-y-1 text-center">
            <h1 class="text-base font-bold text-slate-900 dark:text-white tracking-tight">
                {{ __('Récupération du mot de passe') }}
            </h1>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                {{ __('Saisissez votre e-mail professionnel Maviance pour recevoir un lien éphémère.') }}
            </p>
        </div>

        <!-- Badge de Sécurité FinTech : Expiration 15 minutes -->
        <div class="p-3 rounded-xl bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60 flex items-start gap-2.5 text-xs text-blue-900 dark:text-blue-200">
            <svg class="w-4 h-4 text-[#0020B2] dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <div class="space-y-0.5 font-mono text-[11px]">
                <p class="font-bold">{{ __('Sécurité OWASP / FinTech') }}</p>
                <p class="text-blue-700 dark:text-blue-300">{{ __('Le lien généré expire automatiquement au bout de 15 minutes.') }}</p>
            </div>
        </div>

        <!-- Notification de Statut de Session -->
        @if (session('status'))
            <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-mono">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulaire de Récupération -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Identifiant / Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('Email professionnel Maviance') }}
                </label>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       placeholder="ex: prenom.nom@maviance.cm"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#0020B2] dark:focus:ring-blue-500 focus:border-transparent transition">
                @error('email')
                    <p class="text-[11px] font-mono text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bouton d'Envoi Sécurisé -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-[#0020B2] hover:bg-[#001ca0] text-white text-xs font-semibold shadow-xs hover:shadow-md transition duration-150 cursor-pointer flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span>{{ __('Envoyer le lien sécurisé (15 min)') }}</span>
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

</body>
</html>
