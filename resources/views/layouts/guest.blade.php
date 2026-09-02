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

        <title>{{ config('app.name', 'VigilCore') }} — Connexion</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 dark:bg-[#070b14] text-slate-900 dark:text-slate-100 transition-colors duration-200 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-4 sm:pt-0">
            <div class="flex items-center gap-3 mb-4">
                <a href="/" class="flex items-center gap-3 group">
                    <x-application-logo class="w-12 h-12" />
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-xl tracking-tight text-slate-900 dark:text-white">VigilCore</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">OPS-01</span>
                        </div>
                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-500">Accès Sécurisé Supervision</p>
                    </div>
                </a>
            </div>

            <div style="width: 100% !important; max-width: 420px !important; margin: 0 auto !important;"
                 class="w-full sm:max-w-md px-6 py-6 bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-xl rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
