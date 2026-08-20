<div x-data="{ 
        darkMode: localStorage.getItem('vigilcore_theme') === 'dark' || (!('vigilcore_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
     }" 
     x-init="
        if (darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('vigilcore_theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('vigilcore_theme', 'light');
        }
        $watch('darkMode', val => { 
            localStorage.setItem('vigilcore_theme', val ? 'dark' : 'light'); 
            if(val) { document.documentElement.classList.add('dark'); } 
            else { document.documentElement.classList.remove('dark'); } 
        });
     "
     class="min-h-screen bg-slate-100 dark:bg-[#070b14] text-slate-800 dark:text-slate-200 font-sans antialiased p-3 sm:p-5 lg:p-6 space-y-5 transition-colors duration-200">
    
    <div class="max-w-7xl mx-auto space-y-5">

        <!-- ==================================================== -->
        <!-- BANNIÈRE EXCLUSIVE IMPRESSION & EXPORT PDF (EXCEL-STYLE) -->
        <!-- ==================================================== -->
        <div class="print-only print-header-banner">
            <div style="display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #334155; padding-bottom:8px;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <img src="{{ asset('images/logo.svg') }}" style="width:38px; height:38px; object-fit:contain;">
                    <div>
                        <h1>{{ __('VIGILCORE OPS-01 — RAPPORT D\'AUDIT & ANALYTICS SLA') }}</h1>
                        <p>{{ __('Plateforme de Supervision, Surveillance des Services & Corrélation d\'Alertes') }}</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span class="sla-badge">
                        SLA : {{ $uptimePct }}% {{ __('CONFORME') }}
                    </span>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:8pt; padding-top:6px; font-family:monospace;">
                <span>{{ __('Période analysée') }} : <b>{{ strtoupper($period) }}</b></span>
                <span>{{ __('Date d\'extraction') }} : <b>{{ now()->format('d/m/Y H:i:s') }}</b></span>
                <span>{{ __('Hôte') }} : <b>srv901529 ({{ __('Production') }})</b></span>
            </div>
        </div>


        <!-- ==================================================== -->
        <!-- 1. EN-TÊTE DE NAVIGATION (100% MOBILE-FIRST ADAPTATIF)-->
        <!-- ==================================================== -->
        <header class="no-print sticky top-2 sm:top-3 z-40 flex items-center justify-between gap-2 sm:gap-4 p-2.5 sm:px-4 rounded-xl sm:rounded-2xl bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-200">

            <!-- Gauche : Logo & Horloge -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('images/logo.svg') }}" alt="VigilCore Logo" class="h-8 w-8 sm:h-9 sm:w-9 object-contain group-hover:scale-105 transition-transform">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="font-extrabold text-base sm:text-lg tracking-tight text-slate-900 dark:text-white">VigilCore</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-50 dark:bg-blue-950/80 text-[#0020B2] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            OPS-01
                        </span>
                    </div>
                </a>

                <!-- Horloge Dynamique Desktop -->
                <div x-data="{
                        time: '',
                        updateTime() {
                            const d = new Date();
                            this.time = d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        }
                     }"
                     x-init="updateTime(); setInterval(() => updateTime(), 1000)"
                     class="hidden lg:flex items-center gap-1.5 text-xs font-mono text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-[#0c101a] px-2.5 py-1 rounded-xl border border-slate-200 dark:border-slate-800"
                     title="Fuseau Horaire : Africa/Douala (WAT UTC+1)">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-semibold text-slate-800 dark:text-slate-200" x-text="time"></span>
                    <span class="text-[10px] text-slate-400">WAT</span>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- DROITE (DESKTOP) : TOUS LES CONTRÔLES EN LIGNE -->
            <!-- ============================================== -->
            <div class="hidden md:flex items-center gap-2">

                <!-- Sélecteur de Langue -->
                <div class="relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
                    <button @click="langOpen = !langOpen" 
                            type="button" 
                            title="{{ __('Changer de langue') }}"
                            class="px-2.5 py-1.5 text-xs font-mono font-bold rounded-xl bg-slate-50 dark:bg-[#0c101a] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/60 cursor-pointer transition flex items-center gap-1">
                        <span>{{ app()->getLocale() === 'en' ? '🇬🇧 EN' : '🇫🇷 FR' }}</span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="langOpen" 
                         x-transition
                         class="absolute right-0 mt-1.5 w-32 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 p-1 space-y-0.5 text-xs font-mono"
                         style="display: none;">
                        <button type="button" wire:click="switchLocale('fr')" @click="langOpen = false" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-blue-50 dark:bg-blue-950/60 text-[#0020B2] font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <span>🇫🇷 FR</span>
                            @if(app()->getLocale() === 'fr') <span class="text-xs">✓</span> @endif
                        </button>
                        <button type="button" wire:click="switchLocale('en')" @click="langOpen = false" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg {{ app()->getLocale() === 'en' ? 'bg-blue-50 dark:bg-blue-950/60 text-[#0020B2] font-bold' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                            <span>🇬🇧 EN</span>
                            @if(app()->getLocale() === 'en') <span class="text-xs">✓</span> @endif
                        </button>
                    </div>
                </div>

                <!-- Toggle Thème Dark / Light -->
                <button @click="darkMode = !darkMode" type="button" class="btn-interactive p-2 text-xs font-semibold rounded-xl bg-slate-50 dark:bg-[#0c101a] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition flex items-center cursor-pointer" title="{{ __('Basculer le thème') }}">
                    <svg class="w-4 h-4 text-amber-500" x-show="!darkMode" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-4 h-4 text-indigo-400" x-show="darkMode" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Retour Dashboard Live -->
                <a href="{{ route('dashboard') }}" wire:navigate class="btn-interactive px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-[#0c101a] hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 text-xs font-mono font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-[#0020B2] dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>{{ __('Monitoring Live') }}</span>
                </a>

                <!-- Menu Déroulant Exporter (PDF / Excel) -->
                <div class="relative" x-data="{ exportOpen: false }" @click.outside="exportOpen = false">
                    <button @click="exportOpen = !exportOpen" 
                            type="button" 
                            class="btn-interactive px-3.5 py-1.5 rounded-xl bg-[#0020B2] hover:bg-[#001ca0] text-white text-xs font-mono font-bold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span>{{ __('Exporter') }}</span>
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': exportOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="exportOpen" 
                         x-transition
                         class="absolute right-0 mt-1.5 w-56 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 p-1.5 space-y-1 text-xs font-mono"
                         style="display: none;">
                        
                        <!-- Option 1 : Imprimer / Télécharger PDF -->
                        <button @click="exportOpen = false; window.print()" 
                                type="button" 
                                class="w-full px-3 py-2 rounded-lg text-left text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2.5 cursor-pointer">
                            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656" />
                            </svg>
                            <div>
                                <span class="font-bold block text-slate-900 dark:text-white">{{ __('Rapport PDF') }}</span>
                                <span class="text-[10px] text-slate-400">{{ __('Imprimer ou enregistrer en PDF') }}</span>
                            </div>
                        </button>

                        <!-- Option 2 : Export Excel (.xls) -->
                        <a href="{{ route('reports.export', ['period' => $period, 'severity' => $severityFilter, 'search' => $search]) }}" 
                           @click="exportOpen = false"
                           class="w-full px-3 py-2 rounded-lg text-left text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2.5 cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            <div>
                                <span class="font-bold block text-slate-900 dark:text-white">{{ __('Rapport Excel (.xls)') }}</span>
                                <span class="text-[10px] text-slate-400">{{ __('Statistiques complètes & audit') }}</span>
                            </div>
                        </a>
                    </div>
                </div>

                @auth
                <!-- Menu Profil Déroulant Desktop -->
                <div class="relative pl-1 border-l border-slate-200 dark:border-slate-800" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                    <button @click="userMenu = !userMenu" 
                            type="button" 
                            class="flex items-center gap-2 bg-slate-50 dark:bg-[#0c101a] px-2.5 py-1 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 cursor-pointer transition">
                        <div class="w-6 h-6 rounded-full bg-[#0020B2] text-white flex items-center justify-center text-[11px] font-bold font-mono">
                            {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                        </div>
                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 font-mono">
                            {{ auth()->user()->name ?? 'Caleb' }}
                        </span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="userMenu" 
                         x-transition
                         class="absolute right-0 mt-1.5 w-52 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 p-2 space-y-2 text-xs font-mono"
                         style="display: none;">
                        <div class="px-2 py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ auth()->user()->name ?? 'Opérateur' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'calebdassi@gmail.com' }}</p>
                            <div class="mt-1 flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>{{ __('Opérateur Connecté') }}</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-2.5 py-1.5 rounded-lg text-left text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition flex items-center gap-2 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                <span>{{ __('Se déconnecter') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth

            </div>

            <!-- ============================================== -->
            <!-- DROITE (MOBILE) : 2 BOUTONS ÉPURÉS MAXIMUM     -->
            <!-- ============================================== -->
            <div class="flex md:hidden items-center gap-1.5" x-data="{ mobileMenu: false }">
                
                <!-- 1. Bouton Retour Live Direct -->
                <a href="{{ route('dashboard') }}" 
                   title="{{ __('Monitoring Live') }}"
                   class="p-2 text-xs rounded-xl bg-slate-50 dark:bg-[#0c101a] text-[#0020B2] dark:text-blue-400 border border-slate-200 dark:border-slate-800 active:bg-slate-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>

                <!-- 2. Bouton Menu Complet Mobile (Avatar / Hamburger) -->
                <button @click="mobileMenu = !mobileMenu" 
                        type="button" 
                        class="flex items-center gap-1 bg-[#0020B2] text-white p-1.5 px-2 rounded-xl text-xs font-mono font-bold shadow-xs cursor-pointer">
                    <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">
                        {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                    </div>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Tiroir Mobile Flottant Élégant -->
                <div x-show="mobileMenu" 
                     @click.outside="mobileMenu = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-3 top-16 w-72 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl z-50 p-3 space-y-3 font-mono text-xs"
                     style="display: none;">
                    
                    <!-- En-tête Profil Utilisateur -->
                    <div class="pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Caleb Dassi' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'calebdassi@gmail.com' }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                            En ligne
                        </span>
                    </div>

                    <!-- Exports Mobiles Rapides -->
                    <div class="space-y-1.5">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ __('Exporter') }}</p>
                        <button @click="mobileMenu = false; window.print()" type="button" class="w-full flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-bold">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656" />
                                </svg>
                                {{ __('Rapport PDF') }}
                            </span>
                            <span>↓</span>
                        </button>
                        <a href="{{ route('reports.export', ['period' => $period, 'severity' => $severityFilter, 'search' => $search]) }}" class="w-full flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-bold">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                {{ __('Rapport Excel (.xls)') }}
                            </span>
                            <span>↓</span>
                        </a>
                    </div>

                    <!-- Options Système (Langue & Thème) -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2">
                        <button type="button" wire:click="switchLocale('{{ app()->getLocale() === 'fr' ? 'en' : 'fr' }}')" @click="mobileMenu = false" class="p-2 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ app()->getLocale() === 'fr' ? '🇬🇧 English' : '🇫🇷 Français' }}
                        </button>

                        <button @click="darkMode = !darkMode" type="button" class="p-2 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1">
                            <span x-text="darkMode ? '☀️ Clair' : '🌙 Sombre'"></span>
                        </button>
                    </div>

                    <!-- Déconnexion -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full p-2 rounded-xl text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/40 font-bold flex items-center justify-center gap-2 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                <span>{{ __('Se déconnecter') }}</span>
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        </header>


        <!-- ==================================================== -->
        <!-- 2. CARTES KPIS SUPÉRIEURES (EXCEL / SCREEN STYLÉES)  -->
        <!-- ==================================================== -->
        <div class="animate-entrance stagger-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- TOTAL INCIDENTS -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">{{ __('Total Incidents') }}</span>
                    <span class="no-print p-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="kpi-total-val text-3xl font-extrabold font-mono text-slate-900 dark:text-white">{{ $totalCount }}</span>
                    <span class="badge-res px-2 py-0.5 rounded text-[10px] font-mono font-bold">{{ $resolvedCount }} {{ __('Incidents Clôturés') }}</span>
                </div>
                <div class="no-print w-full bg-slate-100 dark:bg-slate-800/60 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full" style="width: {{ $resolutionRate }}%"></div>
                </div>
            </div>

            <!-- DISPONIBILITÉ GLOBALE (SLA) -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> {{ __('Disponibilité SLA Globale') }}
                    </span>
                    <span class="badge-res px-2 py-0.5 rounded text-[10px] font-mono font-bold">{{ __('Objectif') }} ≥ 99.5%</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="kpi-sla-val text-3xl font-extrabold font-mono text-emerald-600 dark:text-emerald-400">{{ $uptimePct }}%</span>
                    <span class="text-[11px] font-mono text-slate-400">Production</span>
                </div>
                <div class="no-print w-full bg-slate-100 dark:bg-slate-800/60 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full" style="width: {{ $uptimePct }}%"></div>
                </div>
            </div>

            <!-- MTTR MOYEN -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-amber-500 tracking-wider">{{ __('Temps Moyen de Résolution (MTTR)') }}</span>
                    <span class="no-print p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-500 border border-amber-200 dark:border-amber-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="kpi-mttr-val text-3xl font-extrabold font-mono text-amber-500 dark:text-amber-400">{{ $mttrFormatted }}</span>
                    <span class="text-[11px] font-mono text-slate-400">srv901529</span>
                </div>
                <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400 truncate">
                    {{ __('Temps moyen de rétablissement') }}
                </div>
            </div>

            <!-- N8N PIPELINE AUTOMATION -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-cyan-600 dark:text-cyan-400 tracking-wider">{{ __('Taux de Résolution') }}</span>
                    <span class="no-print p-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="kpi-n8n-val text-3xl font-extrabold font-mono text-cyan-600 dark:text-cyan-400">{{ $resolutionRate }}%</span>
                    <span class="badge-res text-[11px] font-mono font-bold">24/7 Live</span>
                </div>
                <div class="no-print w-full bg-slate-100 dark:bg-slate-800/60 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-500 to-indigo-500 h-full rounded-full" style="width: {{ $resolutionRate }}%"></div>
                </div>
            </div>

        </div>

        <!-- ==================================================== -->
        <!-- 3. PANNEAUX D'ANALYSE MÉTIER                        -->
        <!-- ==================================================== -->
        <div class="animate-entrance stagger-2 grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Top 5 Services impactés -->
            <div class="dash-card lg:col-span-2 p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div>
                        <h3 class="text-xs font-mono font-bold uppercase text-slate-800 dark:text-slate-200">
                            {{ __('TOP SERVICES IMPACTÉS & STABILITÉ') }}
                        </h3>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ __('Volume de pannes et fréquence par composant') }}</p>
                    </div>
                    <span class="badge-info px-2 py-0.5 rounded text-[10px] font-mono font-bold">
                        Top 5
                    </span>
                </div>

                @if(empty($topServices))
                    <div class="py-8 text-center text-xs font-mono text-slate-400">
                        {{ __('Tous les services sont opérationnels et conformes aux SLAs') }}
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topServices as $service)
                            <div class="service-row-box p-3 rounded-xl bg-slate-50 dark:bg-[#090e1a] border border-slate-200/80 dark:border-slate-800/80 space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-mono">
                                     <span class="font-bold text-slate-900 dark:text-white truncate pr-2">{{ $service['name'] }}</span>
                                     <span class="text-slate-600 dark:text-slate-400 font-semibold flex-shrink-0">{{ $service['count'] }} {{ __('alerte(s)') }} ({{ $service['pct'] }}%)</span>
                                </div>
                                <div class="w-full bg-slate-200 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                     <div class="bg-gradient-to-r from-rose-500 via-amber-500 to-indigo-500 h-full rounded-full" style="width: {{ $service['pct'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Répartition par Sévérité -->
            <div class="dash-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <h3 class="text-xs font-mono font-bold uppercase text-slate-800 dark:text-slate-200">
                        {{ __('RÉPARTITION PAR CRITICITÉ') }}
                    </h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ __('Classification opérationnelle des alertes') }}</p>
                </div>

                <div class="space-y-2.5">
                    <div class="crit-box-critical flex items-center justify-between p-3 rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <span class="badge-crit text-xs font-mono font-bold">CRITICAL</span>
                        </div>
                        <span class="text-sm font-mono font-extrabold text-rose-600 dark:text-rose-400">{{ $criticalCount }}</span>
                    </div>

                    <div class="crit-box-warning flex items-center justify-between p-3 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            <span class="badge-warn text-xs font-mono font-bold">WARNING</span>
                        </div>
                        <span class="text-sm font-mono font-extrabold text-amber-600 dark:text-amber-400">{{ $warningCount }}</span>
                    </div>

                    <div class="crit-box-info flex items-center justify-between p-3 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                            <span class="badge-info text-xs font-mono font-bold">INFO</span>
                        </div>
                        <span class="text-sm font-mono font-extrabold text-blue-600 dark:text-blue-400">{{ $infoCount }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-mono text-slate-500 dark:text-slate-400">
                    {{ __('Moteur de Corrélation') }} : <b>Kibana Logs + Zabbix + n8n</b>
                </div>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 4. TABLEAU D'AUDIT & REGISTRE D'ÉVÉNEMENTS           -->
        <!-- ==================================================== -->
        <div class="animate-entrance stagger-3 dash-card rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm overflow-hidden">
            <!-- Barre d'outils et filtres (Écran uniquement) -->
            <div class="no-print p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800/80 flex flex-col lg:flex-row lg:items-center justify-between gap-3.5 bg-slate-50/70 dark:bg-[#090e1a]/70">
                <div>
                    <h3 class="text-xs font-mono font-bold uppercase text-slate-800 dark:text-slate-200">
                        {{ __('Historique Complet des Incidents') }}
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                        {{ __('Structure brute transmise par les sondes de surveillance') }}
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Sélecteur Période en Pilules -->
                    <div class="flex items-center gap-1 bg-slate-200/80 dark:bg-[#070b14] p-1 rounded-xl border border-slate-300 dark:border-slate-800 text-xs font-mono">
                        <button wire:click="setPeriod('today')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'today' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('Aujourd\'hui') }}
                        </button>
                        <button wire:click="setPeriod('week')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'week' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('7 derniers jours') }}
                        </button>
                        <button wire:click="setPeriod('month')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'month' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('30 derniers jours') }}
                        </button>
                        <button wire:click="setPeriod('all')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'all' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            {{ __('Tous') }}
                        </button>
                    </div>

                    <!-- Champ Recherche -->
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Rechercher un incident...') }}" class="px-2.5 py-1.5 rounded-xl bg-white dark:bg-[#070b14] border border-slate-200 dark:border-slate-700/80 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-indigo-500 font-mono">

                    <!-- Filtre Sévérité -->
                    <select wire:model.live="severityFilter" class="px-2.5 py-1.5 rounded-xl bg-white dark:bg-[#070b14] border border-slate-200 dark:border-slate-700/80 text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-indigo-500 font-mono cursor-pointer">
                        <option value="all">{{ __('Toutes les sévérités') }}</option>
                        <option value="critical">{{ __('Critiques') }}</option>
                        <option value="warning">{{ __('Avertissements') }}</option>
                        <option value="info">{{ __('Informations') }}</option>
                    </select>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- VUE 1 (DESKTOP) : GRAND TABLEAU D'AUDIT        -->
            <!-- ============================================== -->
            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-[#1e293b] text-white uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-3 text-center w-12">ID</th>
                            <th class="py-3 px-3 w-40">{{ __('Horodatage') }}</th>
                            <th class="py-3 px-3">{{ __('Incident') }} / {{ __('Service') }}</th>
                            <th class="py-3 px-3 w-28 text-center">{{ __('Gravité') }}</th>
                            <th class="py-3 px-3 w-24 text-center">{{ __('Statut') }}</th>
                            <th class="py-3 px-3 w-24 text-center">{{ __('Durée (MTTR)') }}</th>
                            <th class="py-3 px-3 w-36">{{ __('Source') }}</th>
                            <th class="py-3 px-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                        @forelse($incidents as $incident)
                            @php
                                $mttr = '--';
                                if ($incident->status === 'resolved' && $incident->created_at && $incident->updated_at) {
                                    $diffSec = $incident->created_at->diffInSeconds($incident->updated_at);
                                    $mttr = ($diffSec >= 60) ? (floor($diffSec / 60) . 'm ' . ($diffSec % 60) . 's') : ($diffSec . 's');
                                }
                            @endphp
                            <tr class="incident-row hover:bg-slate-50 dark:hover:bg-slate-800/30 transition">
                                <td class="py-2.5 px-3 font-bold text-slate-500 dark:text-slate-400 text-center">#{{ $incident->id }}</td>
                                <td class="py-2.5 px-3 text-slate-700 dark:text-slate-300 whitespace-nowrap text-[11px]">
                                    {{ $incident->created_at ? $incident->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                </td>
                                <td class="py-2.5 px-3 font-bold text-slate-900 dark:text-white">
                                    {{ $incident->title }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if(strtoupper($incident->severity) === 'CRITICAL')
                                        <span class="badge-crit">CRITICAL</span>
                                    @elseif(strtoupper($incident->severity) === 'WARNING')
                                        <span class="badge-warn">WARNING</span>
                                    @else
                                        <span class="badge-info">INFO</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($incident->status === 'resolved')
                                        <span class="badge-res">{{ __('Résolu') }}</span>
                                    @else
                                        <span class="badge-open">{{ __('En cours') }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 font-bold text-amber-600 dark:text-amber-400 text-center">
                                    {{ $mttr }}
                                </td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-600 dark:text-slate-400">
                                    {{ $incident->source ?? 'Kibana Logs Engine' }}
                                </td>
                                <td class="py-2.5 px-3 text-right no-print whitespace-nowrap">
                                    <button wire:click="viewJson({{ $incident->id }})" 
                                            class="btn-interactive px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-[#161c2e] hover:bg-slate-200 dark:hover:bg-slate-800 text-purple-700 dark:text-purple-400 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold cursor-pointer transition">
                                        JSON
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 font-mono text-xs">
                                    {{ __('Aucun incident trouvé avec ce filtre.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ============================================== -->
            <!-- VUE 2 (MOBILE) : CARTES D'AUDIT TACTILES       -->
            <!-- ============================================== -->
            <div class="block md:hidden p-3 space-y-3">
                @forelse($incidents as $incident)
                    @php
                        $mttr = '--';
                        if ($incident->status === 'resolved' && $incident->created_at && $incident->updated_at) {
                            $diffSec = $incident->created_at->diffInSeconds($incident->updated_at);
                            $mttr = ($diffSec >= 60) ? (floor($diffSec / 60) . 'm ' . ($diffSec % 60) . 's') : ($diffSec . 's');
                        }
                    @endphp
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 space-y-2 font-mono text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-400">#{{ $incident->id }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $incident->created_at ? $incident->created_at->format('d/m H:i') : '' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1">
                                @if(strtoupper($incident->severity) === 'CRITICAL')
                                    <span class="badge-crit">CRIT</span>
                                @elseif(strtoupper($incident->severity) === 'WARNING')
                                    <span class="badge-warn">WARN</span>
                                @else
                                    <span class="badge-info">INFO</span>
                                @endif

                                @if($incident->status === 'resolved')
                                    <span class="badge-res">{{ __('Résolu') }}</span>
                                @else
                                    <span class="badge-open">{{ __('En cours') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="font-bold text-slate-900 dark:text-white text-xs">
                            {{ $incident->title }}
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                            <span>📡 {{ $incident->source ?? 'Kibana' }}</span>
                            <span class="text-amber-500 font-bold">⏱️ {{ $mttr }}</span>
                        </div>

                        <div class="pt-1">
                            <button wire:click="viewJson({{ $incident->id }})" 
                                    class="w-full py-1.5 rounded-lg bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-center font-bold text-xs cursor-pointer">
                                📄 Payload JSON
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 font-mono text-xs">
                        {{ __('Aucun incident trouvé avec ce filtre.') }}
                    </div>
                @endforelse
            </div>

            <!-- Pagination (Écran uniquement) -->
            <div class="no-print p-3 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-[#070b14]">
                {{ $incidents->links() }}
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- PIED DE PAGE IMPRESSION / PDF (EXCEL-STYLE)          -->
        <!-- ==================================================== -->
        <div class="print-only print-footer-banner">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span>🛡️ <b>VigilCore OPS-01</b> — {{ __('Rapport d\'exploitation et conformité SLA généré automatiquement') }}</span>
                <span>{{ __('Document strictement confidentiel — Usage interne') }}</span>
            </div>
        </div>

    </div>

    <!-- ==================================================== -->
    <!-- 5. MODALE D'INSPECTION JSON ULTRA-RÉACTIVE & DESIGN -->
    <!-- ==================================================== -->
    @if($showJsonModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/75 backdrop-blur-sm animate-fade-in"
             wire:click.self="closeModal"
             @keydown.escape.window="$wire.closeModal()">
            
            <div class="bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-2xl max-w-2xl w-full p-4 sm:p-5 shadow-2xl space-y-3 transform transition-all animate-scale-up"
                 x-data="{ copied: false }">
                
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-950/80 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-mono font-bold uppercase text-slate-900 dark:text-white">
                                {{ $selectedIncidentTitle ?? 'Raw Incident Telemetry' }}
                            </h3>
                            <p class="text-[10px] text-slate-400 font-mono">{{ __('Payload JSON formaté & horodaté') }}</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="relative bg-[#090d16] text-emerald-400 p-4 rounded-xl font-mono text-[11px] sm:text-xs overflow-x-auto max-h-96 border border-slate-800 leading-relaxed custom-scrollbar shadow-inner select-all">
                    <pre class="whitespace-pre font-mono">{{ json_encode($activeJsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                
                <div class="flex items-center justify-between pt-1">
                    <button type="button" 
                            @click="navigator.clipboard.writeText(JSON.stringify({{ json_encode($activeJsonPayload) }}, null, 2)); copied = true; setTimeout(() => copied = false, 2000)" 
                            class="btn-interactive px-3 py-1.5 rounded-xl text-xs font-mono font-semibold bg-slate-100 dark:bg-[#161c2e] hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 cursor-pointer transition">
                        <svg class="w-3.5 h-3.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span x-text="copied ? '✓ {{ __('Copié dans le presse-papier !') }}' : '{{ __('Copier le JSON') }}'"></span>
                    </button>

                    <button wire:click="closeModal" class="btn-interactive px-4 py-1.5 text-xs font-mono font-semibold rounded-xl bg-purple-600 hover:bg-purple-700 text-white cursor-pointer shadow-xs transition">
                        {{ __('Fermer') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

