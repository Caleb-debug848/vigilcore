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
            <div class="flex items-center justify-center mb-6">
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#0020B2]/15 to-blue-600/20 dark:from-blue-500/20 dark:to-blue-900/40 border border-blue-500/30 dark:border-blue-400/30 shadow-md flex items-center justify-center p-1.5 group-hover:scale-105 transition-transform">
                        <img src="{{ asset('images/logo.svg') }}" alt="VigilCore" class="w-full h-full object-contain">
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xl tracking-tight text-slate-900 dark:text-white uppercase font-sans leading-none">
                                VIGIL<span class="text-[#2563EB] dark:text-[#38BDF8]">CORE</span>
                            </span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-black bg-[#0020B2] text-white dark:bg-[#2563EB] tracking-wider uppercase">
                                NOC
                            </span>
                        </div>
                        <span class="text-[10px] font-mono font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider pt-0.5">
                            Supervision Passerelles
                        </span>
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
