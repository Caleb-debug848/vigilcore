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

        <title>{{ config('app.name', 'VigilCore') }} — Platform</title>

        <!-- Favicon Officiel VigilCore -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=4">
        <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}?v=4">

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
                            <span class="font-extrabold text-2xl tracking-tight text-slate-900 dark:text-white">
                                Vigil<span class="text-[#2563EB] dark:text-blue-400">Core</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[9.5px] font-mono font-bold bg-blue-50 dark:bg-blue-950/70 text-[#0020B2] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                ENTERPRISE
                            </span>
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
