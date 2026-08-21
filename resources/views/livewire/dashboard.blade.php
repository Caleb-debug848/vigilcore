<div x-data="{ 
        darkMode: localStorage.getItem('vigilcore_theme') === 'dark' || (!('vigilcore_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('vigilcore_theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
     }" 
     :class="{ 'dark': darkMode }" 
     class="min-h-screen antialiased relative w-full bg-[#f8fafc] dark:bg-[#090d16] text-slate-900 dark:text-slate-100 transition-colors duration-200">

    <div class="min-h-screen px-3 py-3 sm:px-6 sm:py-5 max-w-7xl mx-auto space-y-4 sm:space-y-5">

        <!-- ==================================================== -->
        <!-- 1. EN-TÊTE DE NAVIGATION (STREAMLINED & MOBILE-FIRST)-->
        <!-- ==================================================== -->        <!-- ==================================================== -->
        <!-- 1. EN-TÊTE DE NAVIGATION (100% MOBILE-FIRST ADAPTATIF)-->
        <!-- ==================================================== -->
        <header class="flex items-center justify-between gap-2 sm:gap-4 p-2.5 sm:px-4 bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-xs transition-colors">
            
            <!-- Gauche : Logo & Badge OPS-01 -->
            <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                    <img src="{{ asset('images/logo.svg') }}" alt="VigilCore Logo" class="h-8 w-8 sm:h-9 sm:w-9 object-contain group-hover:scale-105 transition-transform">
                    <div class="flex items-center gap-1.5 sm:gap-2">
                        <span class="font-extrabold text-base sm:text-lg tracking-tight text-slate-900 dark:text-white">VigilCore</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-50 dark:bg-blue-950/80 text-[#0020B2] dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            OPS-01
                        </span>
                        <span class="relative flex h-2 w-2">
                            <span class="radar-live absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
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
                
                <!-- Bouton Actualiser Manuel -->
                <button wire:click="refreshData" 
                        type="button" 
                        title="{{ __('Actualiser les incidents (Manuel)') }}"
                        class="p-2 text-xs font-semibold rounded-xl bg-slate-50 dark:bg-[#0c101a] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/60 cursor-pointer transition flex items-center gap-1.5 group">
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-[#0020B2] dark:group-hover:text-blue-400 transition" 
                         wire:loading.class="animate-spin text-[#0020B2]"
                         wire:target="refreshData"
                         fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span class="font-mono">{{ __('Actualiser') }}</span>
                </button>

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

                <!-- Toggle Thème Clair / Sombre -->
                <button type="button" 
                        @click="toggleTheme()" 
                        title="{{ __('Basculer le thème') }}"
                        class="p-2 text-xs font-semibold rounded-xl bg-slate-50 dark:bg-[#0c101a] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/60 cursor-pointer transition flex items-center">
                    <svg x-show="!darkMode" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="darkMode" class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Bouton Rapports & SLA -->
                <a href="{{ route('reports') }}" wire:navigate class="px-3 py-1.5 rounded-xl bg-[#0020B2] hover:bg-[#001ca0] text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <span>{{ __('Rapports') }}</span>
                </a>

                @if(app()->environment('local', 'staging') || config('app.debug'))
                <!-- Bouton Simulateur Ops -->
                <button wire:click="openSimulationHub" 
                        type="button" 
                        title="{{ __('Simulateur d\'Incidents') }}"
                        class="px-2.5 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/60 text-xs font-mono font-bold transition flex items-center gap-1 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>20 Ops</span>
                </button>
                @endif

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
                
                <!-- 1. Bouton Actualiser Manuel Rapide -->
                <button wire:click="refreshData" 
                        type="button" 
                        title="{{ __('Actualiser') }}"
                        class="p-2 text-xs rounded-xl bg-slate-50 dark:bg-[#0c101a] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 active:bg-slate-200 cursor-pointer">
                    <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" 
                         wire:loading.class="animate-spin text-[#0020B2]"
                         wire:target="refreshData"
                         fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                </button>

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

                    <!-- Navigation Rapide -->
                    <div class="space-y-1">
                        <a href="{{ route('reports') }}" wire:navigate class="flex items-center justify-between p-2 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 text-[#0020B2] dark:text-blue-300 font-bold">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                {{ __('Rapports & SLA') }}
                            </span>
                            <span>→</span>
                        </a>

                        @if(app()->environment('local', 'staging') || config('app.debug'))
                        <button @click="mobileMenu = false; $wire.openSimulationHub()" type="button" class="w-full flex items-center justify-between p-2 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 font-bold text-left">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                {{ __('Simulateur d\'Incidents (20)') }}
                            </span>
                            <span class="text-[10px] bg-amber-200 dark:bg-amber-900 px-1.5 py-0.2 rounded">TEST</span>
                        </button>
                        @endif
                    </div>

                    <!-- Options Système (Langue & Thème) -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2">
                        <!-- Langue Toggle -->
                        <button type="button" wire:click="switchLocale('{{ app()->getLocale() === 'fr' ? 'en' : 'fr' }}')" @click="mobileMenu = false" class="p-2 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-700 dark:text-slate-300">
                            {{ app()->getLocale() === 'fr' ? '🇬🇧 English' : '🇫🇷 Français' }}
                        </button>

                        <!-- Thème Toggle -->
                        <button @click="toggleTheme()" type="button" class="p-2 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 text-center font-bold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-1">
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
        <!-- 2. CARTES SYNTHÉTIQUES (3 MÉTRIQUES ESSENTIELLES)    -->
        <!-- ==================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            
            <!-- Carte 1 : Incidents Actifs -->
            <div class="dash-card-studio p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                    <span class="text-xs font-semibold">{{ __('Incidents Actifs') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $totalActive > 0 ? 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/60 dark:text-red-400 dark:border-red-900/60 animate-pulse' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-900/60' }}">
                        {{ $totalActive > 0 ? __('Action Requise') : __('Nominal') }}
                    </span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight {{ $totalActive > 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white' }}">
                        {{ $totalActive }}
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">
                        {{ $activeCrit }} {{ __('Critiques') }} · {{ $activeWarn }} {{ __('Avertissements') }}
                    </span>
                </div>
            </div>

            <!-- Carte 2 : Disponibilité Globale SLA -->
            <div class="dash-card-studio p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                    <span class="text-xs font-semibold">{{ __('Disponibilité SLA') }}</span>
                    <span class="w-2 h-2 rounded-full bg-[#0020B2]"></span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight text-slate-900 dark:text-white">
                        {{ $uptimePct }}%
                    </span>
                    <span class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                        SLA {{ __('CONFORME') }} (≥ 99.5%)
                    </span>
                </div>
            </div>

            <!-- Carte 3 : Dernière Télémétrie -->
            @php
                $latestInc = $incidents->first();
                $lastSeen = $latestInc ? $latestInc->created_at?->diffForHumans() : __('Système Opérationnel');
            @endphp
            <div class="dash-card-studio p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                    <span class="text-xs font-semibold">{{ __('Dernière Télémétrie') }}</span>
                    <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                </div>
                <div class="mt-3 flex items-baseline justify-between">
                    <span class="text-lg sm:text-xl font-bold font-mono tracking-tight text-slate-900 dark:text-white truncate">
                        {{ $lastSeen }}
                    </span>
                    <span class="text-[11px] font-mono text-slate-400">
                        srv901529
                    </span>
                </div>
            </div>

        </div>

        <!-- ==================================================== -->
        <!-- 2. BIS. CARTOGRAPHIE DES 20 SERVICES & SANTÉ EN DIRECT -->
        <!-- ==================================================== -->
        <div class="dash-card-studio p-4 sm:p-5 space-y-4" 
             x-data="{ 
                showServicesGrid: true, 
                selectedCat: 'all',
                txtHide: @js(__('Masquer')),
                txtExpand: @js(__('Développer'))
             }">
            
            <!-- En-tête de la matrice -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center font-mono font-extrabold text-sm shadow-md shadow-blue-500/20">
                        20
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <span>{{ __('Santé en Direct des 20 Services & Microservices') }}</span>
                            <span class="inline-flex h-2.5 w-2.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ __('Supervision temps réel de l\'écosystème Maviance, Mobile Money, Facturiers et Partenaires') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <!-- Badge État Global -->
                    <span class="px-3 py-1 rounded-full text-xs font-mono font-bold flex items-center gap-1.5 {{ $operationalCount === 20 ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-xs' : 'bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/30 animate-pulse' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $operationalCount === 20 ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        <span>{{ $operationalCount }}/20 {{ __('Opérationnels') }}</span>
                    </span>

                    <!-- Toggle Masquer / Afficher (100% Bilingue FR / EN) -->
                    <button @click="showServicesGrid = !showServicesGrid" 
                            type="button" 
                            class="px-3 py-1 text-xs font-mono font-medium rounded-xl bg-slate-100 dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700/80 transition cursor-pointer flex items-center gap-1">
                        <span x-text="showServicesGrid ? '▲ ' + txtHide : '▼ ' + txtExpand + ' (20)'"></span>
                    </button>
                </div>
            </div>

            <!-- Barre de Filtres par Catégorie (Onglets interactifs) -->
            <div x-show="showServicesGrid" class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar pb-1 pt-0.5">
                <button @click="selectedCat = 'all'" 
                        type="button"
                        :class="selectedCat === 'all' ? 'bg-[#0020B2] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition cursor-pointer whitespace-nowrap">
                    🌐 {{ __('Tous') }} (20)
                </button>
                <button @click="selectedCat = 'maviance'" 
                        type="button"
                        :class="selectedCat === 'maviance' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                    <span>💳</span> <span>{{ __('Plateformes Maviance') }}</span> (4)
                </button>
                <button @click="selectedCat = 'momo'" 
                        type="button"
                        :class="selectedCat === 'momo' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                    <span>📱</span> <span>{{ __('Mobile Money & Télécoms') }}</span> (9)
                </button>
                <button @click="selectedCat = 'utilities'" 
                        type="button"
                        :class="selectedCat === 'utilities' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                    <span>⚡</span> <span>{{ __('Énergie & Eau') }}</span> (2)
                </button>
                <button @click="selectedCat = 'tv'" 
                        type="button"
                        :class="selectedCat === 'tv' ? 'bg-purple-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                    <span>📺</span> <span>{{ __('Télévision & Médias') }}</span> (3)
                </button>
                <button @click="selectedCat = 'regional'" 
                        type="button"
                        :class="selectedCat === 'regional' ? 'bg-teal-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800/60 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-semibold transition cursor-pointer whitespace-nowrap flex items-center gap-1">
                    <span>🤝</span> <span>{{ __('Régional & Partenaires') }}</span> (2)
                </button>
            </div>

            <!-- Grille des 20 Services avec Logos & Design Ultra-Pro -->
            <div x-show="showServicesGrid" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                @foreach($servicesHealth as $service)
                @php
                    $isOk = ($service['status'] === 'operational');
                    $key = $service['key'];
                    $cat = $service['category'];
                @endphp
                <div x-show="selectedCat === 'all' || selectedCat === '{{ $cat }}'"
                     x-transition
                     class="group relative p-3 rounded-2xl border transition-all duration-200 flex items-center justify-between gap-3 {{ $isOk ? 'bg-white dark:bg-[#0c121e]/90 border-slate-200/90 dark:border-slate-800 hover:border-blue-500/50 dark:hover:border-blue-500/50 hover:shadow-md' : 'bg-red-500/5 dark:bg-red-950/30 border-red-500/50 dark:border-red-600 shadow-sm animate-pulse' }}">
                    
                    <!-- Logo / Icône de Marque Dédiée -->
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center shadow-xs transition-transform group-hover:scale-105
                            @if(str_contains($key, 'mtn')) bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20
                            @elseif(str_contains($key, 'orange')) bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20
                            @elseif(str_contains($key, 'camtel')) bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20
                            @elseif(str_contains($key, 'eneo')) bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border border-yellow-500/20
                            @elseif(str_contains($key, 'camwater')) bg-teal-500/10 text-teal-600 dark:text-teal-400 border border-teal-500/20
                            @elseif(str_contains($key, 'canal') || str_contains($key, 'dstv') || str_contains($key, 'startimes')) bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20
                            @elseif(str_contains($key, 'sabc')) bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/20
                            @else bg-blue-500/10 text-[#0020B2] dark:text-blue-400 border border-blue-500/20
                            @endif">
                            
                            @if($key === 'smobilpay')
                                <!-- Smobilpay Platform -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-6-8.25h19.5a2.25 2.25 0 012.25 2.25v8.25a2.25 2.25 0 01-2.25 2.25H2.25A2.25 2.25 0 010 17.25V11.25A2.25 2.25 0 012.25 9z" />
                                </svg>
                            @elseif($key === 's3p')
                                <!-- S3P Merchant API -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                                </svg>
                            @elseif($key === 'merchant_portal')
                                <!-- Merchant Portal -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25A2.25 2.25 0 010 18.75V10.5m18 10.5h3.75a2.25 2.25 0 002.25-2.25V10.5M3.75 6.75h16.5M3.75 6.75a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25m-16.5 0l2.25 3.75m14.25-3.75l-2.25 3.75" />
                                </svg>
                            @elseif($key === 'ecommerce')
                                <!-- E-commerce -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            @elseif(str_contains($key, 'mtn'))
                                <!-- MTN MoMo & Airtime -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            @elseif(str_contains($key, 'orange'))
                                <!-- Orange Money & Airtime -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif(str_contains($key, 'camtel'))
                                <!-- Camtel Telecom -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                                </svg>
                            @elseif(str_contains($key, 'eneo'))
                                <!-- ENEO Electricity -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                </svg>
                            @elseif(str_contains($key, 'camwater'))
                                <!-- Camwater -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                            @elseif(str_contains($key, 'canal') || str_contains($key, 'dstv') || str_contains($key, 'startimes'))
                                <!-- Television & Media -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            @elseif(str_contains($key, 'sabc'))
                                <!-- SABC Distribution -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25H4.875c-.621 0-1.125.504-1.125 1.125v6.75c0 .621.504 1.125 1.125 1.125h9.75V7.5z" />
                                </svg>
                            @else
                                <!-- Regional Partners -->
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253" />
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-[#0020B2] dark:group-hover:text-blue-300 transition-colors" title="{{ $service['name'] }}">
                                {{ $service['name'] }}
                            </p>
                            <span class="text-[10px] font-mono font-medium text-slate-500 dark:text-slate-400 truncate block">
                                {{ __($service['category_label']) }}
                            </span>
                        </div>
                    </div>

                    <!-- Statut du Service -->
                    <div class="flex-shrink-0">
                        @if($isOk)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>OK</span>
                            </span>
                        @else
                            <button wire:click="viewJson({{ $service['incident_id'] }})" 
                                    type="button"
                                    title="{{ __('Inspecter l\'incident actif') }}"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold bg-red-600 hover:bg-red-700 text-white shadow-xs transition cursor-pointer">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                <span>{{ $service['severity'] }}</span>
                            </button>
                        @endif
                    </div>

                </div>
                @endforeach
            </div>

        </div>

        <!-- ==================================================== -->
        <!-- 3. FLUX DES INCIDENTS (ADAPTATIF MOBILE & DESKTOP)   -->
        <!-- ==================================================== -->
        <div class="dash-card-studio p-3.5 sm:p-5 space-y-4">
            
            <!-- Barre d'outils du tableau -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white tracking-tight">{{ __('Registre des Incidents & Alertes') }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Surveillance en direct des 20 services critiques') }}</p>
                </div>

                <!-- Filtres par Criticité Défilables sur Mobile -->
                <div class="flex items-center gap-1.5 overflow-x-auto custom-scrollbar pb-1 sm:pb-0">
                    <button wire:click="setFilter('all')" 
                            class="px-3 py-1.5 rounded-xl text-xs font-mono font-semibold transition cursor-pointer whitespace-nowrap {{ $filter === 'all' ? 'bg-[#0020B2] text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        {{ __('Tous') }} ({{ $totalAll }})
                    </button>
                    <button wire:click="setFilter('critical')" 
                            class="px-3 py-1.5 rounded-xl text-xs font-mono font-semibold transition cursor-pointer whitespace-nowrap {{ $filter === 'critical' ? 'bg-red-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        {{ __('Critiques') }} ({{ $countCrit }})
                    </button>
                    <button wire:click="setFilter('warning')" 
                            class="px-3 py-1.5 rounded-xl text-xs font-mono font-semibold transition cursor-pointer whitespace-nowrap {{ $filter === 'warning' ? 'bg-amber-500 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        {{ __('Avertissements') }} ({{ $countWarn }})
                    </button>
                    <button wire:click="setFilter('info')" 
                            class="px-3 py-1.5 rounded-xl text-xs font-mono font-semibold transition cursor-pointer whitespace-nowrap {{ $filter === 'info' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        {{ __('Informations') }} ({{ $countInfo }})
                    </button>
                </div>

            </div>

            <!-- ============================================== -->
            <!-- VUE 1 (DESKTOP) : GRAND TABLEAU COMPLET       -->
            <!-- ============================================== -->
            <div class="hidden md:block overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-slate-50 dark:bg-[#0c101a] text-slate-600 dark:text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3 w-12 text-center">ID</th>
                            <th class="py-2.5 px-3 w-40">{{ __('Horodatage') }}</th>
                            <th class="py-2.5 px-3">{{ __('Incident') }} / {{ __('Service') }}</th>
                            <th class="py-2.5 px-3 w-28 text-center">{{ __('Gravité') }}</th>
                            <th class="py-2.5 px-3 w-24 text-center">{{ __('Statut') }}</th>
                            <th class="py-2.5 px-3 w-24 text-center">{{ __('Durée (MTTR)') }}</th>
                            <th class="py-2.5 px-3 w-36">{{ __('Source') }}</th>
                            <th class="py-2.5 px-3 w-24 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                        @forelse($incidents as $incident)
                            @php
                                $mttr = '--';
                                if ($incident->status === 'resolved' && $incident->created_at && $incident->updated_at) {
                                    $diffSec = $incident->created_at->diffInSeconds($incident->updated_at);
                                    $mttr = ($diffSec >= 60) ? (floor($diffSec / 60) . 'm ' . ($diffSec % 60) . 's') : ($diffSec . 's');
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition">
                                <td class="py-2.5 px-3 font-bold text-slate-400 text-center">#{{ $incident->id }}</td>
                                <td class="py-2.5 px-3 text-[11px] whitespace-nowrap text-slate-500 dark:text-slate-400">
                                    {{ $incident->created_at ? $incident->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-slate-900 dark:text-white">
                                    {{ $incident->title }}
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if(strtoupper($incident->severity) === 'CRITICAL')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/60 dark:text-red-400 dark:border-red-800">
                                            CRITICAL
                                        </span>
                                    @elseif(strtoupper($incident->severity) === 'WARNING')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-400 dark:border-amber-800">
                                            WARNING
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-400 dark:border-blue-800">
                                            INFO
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    @if($incident->status === 'resolved')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800">
                                            {{ __('Résolu') }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-400 dark:border-rose-800 animate-pulse">
                                            {{ __('En cours') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-2.5 px-3 font-bold text-[#F59E0B] text-center">
                                    {{ $mttr }}
                                </td>
                                <td class="py-2.5 px-3 text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ $incident->source ?? 'Kibana Logs' }}
                                </td>
                                <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                    <button wire:click="viewJson({{ $incident->id }})" 
                                            class="px-2.5 py-1 rounded bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-[11px] font-bold cursor-pointer transition">
                                        JSON
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400 font-mono text-xs">
                                    {{ __('Aucun incident actif') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ============================================== -->
            <!-- VUE 2 (MOBILE) : CARTES D'INCIDENTS TACTILES  -->
            <!-- ============================================== -->
            <div class="block md:hidden space-y-3">
                @forelse($incidents as $incident)
                    @php
                        $mttr = '--';
                        if ($incident->status === 'resolved' && $incident->created_at && $incident->updated_at) {
                            $diffSec = $incident->created_at->diffInSeconds($incident->updated_at);
                            $mttr = ($diffSec >= 60) ? (floor($diffSec / 60) . 'm ' . ($diffSec % 60) . 's') : ($diffSec . 's');
                        }
                    @endphp
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#0c101a] border border-slate-200 dark:border-slate-800 space-y-2.5 font-mono text-xs">
                        
                        <!-- Top Bar de la carte Mobile -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-slate-400">#{{ $incident->id }}</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $incident->created_at ? $incident->created_at->format('d/m H:i') : '' }}
                                </span>
                            </div>

                            <div class="flex items-center gap-1">
                                @if(strtoupper($incident->severity) === 'CRITICAL')
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/60 dark:text-red-400">
                                        CRIT
                                    </span>
                                @elseif(strtoupper($incident->severity) === 'WARNING')
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-400">
                                        WARN
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-400">
                                        INFO
                                    </span>
                                @endif

                                @if($incident->status === 'resolved')
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-400">
                                        {{ __('Résolu') }}
                                    </span>
                                @else
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-400 animate-pulse">
                                        {{ __('En cours') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Titre de l'incident -->
                        <div class="font-bold text-slate-900 dark:text-white text-xs leading-snug">
                            {{ $incident->title }}
                        </div>

                        <!-- Métadonnées (Source & MTTR) -->
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200/60 dark:border-slate-800/60">
                            <span>📡 {{ $incident->source ?? 'Kibana Logs' }}</span>
                            <span class="text-amber-500 font-bold">⏱️ {{ $mttr }}</span>
                        </div>

                        <!-- Actions Mobiles -->
                        <div class="pt-1">
                            <button wire:click="viewJson({{ $incident->id }})" 
                                    class="w-full py-1.5 rounded-lg bg-white dark:bg-[#111827] border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-center font-bold text-xs cursor-pointer">
                                📄 Payload JSON
                            </button>
                        </div>

                    </div>
                @empty
                    <div class="py-6 text-center text-slate-400 font-mono text-xs">
                        {{ __('Aucun incident actif') }}
                    </div>
                @endforelse
            </div>

            <!-- ============================================== -->
            <!-- PAGINATION BILINGUE & NUMÉROTATION DES PAGES   -->
            <!-- ============================================== -->
            @if($incidents->hasPages())
            <div class="pt-4 border-t border-slate-200/60 dark:border-slate-800/60">
                {{ $incidents->links() }}
            </div>
            @endif

        </div>

    </div>

    <!-- ==================================================== -->
    <!-- 4. MODALE D'INSPECTION DU PAYLOAD JSON               -->
    <!-- ==================================================== -->
    @if($showJsonModal && $activeJsonPayload)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-900/60 backdrop-blur-xs animate-entrance"
         wire:click.self="closeModal"
         @keydown.escape.window="$wire.closeModal()">
        
        <div class="w-full max-w-2xl bg-white dark:bg-[#111827] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden space-y-3 p-5"
             x-data="{ copied: false }">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#0020B2]"></span>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white font-mono truncate max-w-md">
                        {{ __('Payload JSON') }} — {{ $selectedIncidentTitle }}
                    </h3>
                </div>
                <button wire:click="closeModal" 
                        class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Contenu JSON brut avec sélection facile -->
            <div class="relative bg-[#090d16] text-emerald-400 p-4 rounded-xl font-mono text-[11px] sm:text-xs overflow-x-auto max-h-96 border border-slate-800 leading-relaxed custom-scrollbar shadow-inner select-all">
                <pre class="whitespace-pre font-mono"><code>{{ json_encode($activeJsonPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</code></pre>
            </div>

            <!-- Barre d'actions : Bouton Copier + Fermer -->
            <div class="flex items-center justify-between pt-2">
                <button type="button" 
                        @click="navigator.clipboard.writeText(JSON.stringify({{ json_encode($activeJsonPayload) }}, null, 2)); copied = true; setTimeout(() => copied = false, 2000)" 
                        class="px-3 py-1.5 rounded-xl text-xs font-mono font-semibold bg-slate-100 dark:bg-[#161c2e] hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 flex items-center gap-1.5 cursor-pointer transition">
                    <svg class="w-3.5 h-3.5 text-[#0020B2] dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                    </svg>
                    <span x-text="copied ? '✓ {{ __('Copié !') }}' : '{{ __('Copier le JSON') }}'"></span>
                </button>

                <button wire:click="closeModal" 
                        class="px-4 py-1.5 rounded-xl bg-[#0020B2] hover:bg-[#001ca0] text-white text-xs font-mono font-semibold cursor-pointer shadow-xs transition">
                    {{ __('Fermer') }}
                </button>
            </div>

        </div>
    </div>
    @endif


    <!-- ==================================================== -->
    <!-- 5. MODALE DU SIMULATEUR OPS (STAGING / LOCAL SEUL)   -->
    <!-- ==================================================== -->
    @if($showSimulationHub && (app()->environment('local', 'staging') || config('app.debug')))
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="w-full max-w-4xl bg-white dark:bg-[#111827] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-4 max-h-[90vh] flex flex-col">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white font-mono flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Simulateur d'Incidents — 20 Services & Webhook n8n
                    </h3>
                    <p class="text-xs text-slate-500 font-mono">Mode Staging / Test Local</p>
                </div>
                <button wire:click="closeSimulationHub" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            @if($simulationFeedback)
            <div class="p-3 rounded-xl {{ $simulationFeedbackType === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-red-50 text-red-800 border border-red-200' }} text-xs font-mono">
                {{ $simulationFeedback }}
            </div>
            @endif

            <!-- Filtres de Catégories de Services (Flex Wrap & Contraste Élevé) -->
            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap text-xs font-mono">
                <button wire:click="setSimulationCategory('all')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'all' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Tous ({{ $allScenariosCount }})
                </button>
                <button wire:click="setSimulationCategory('maviance')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'maviance' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Plateformes Core (4)
                </button>
                <button wire:click="setSimulationCategory('banking')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'banking' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Banques (3)
                </button>
                <button wire:click="setSimulationCategory('utilities')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'utilities' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Factures & Énergie (3)
                </button>
                <button wire:click="setSimulationCategory('telco')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'telco' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Telco & MoMo (4)
                </button>
                <button wire:click="setSimulationCategory('media')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'media' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Télévision & Médias (3)
                </button>
                <button wire:click="setSimulationCategory('corporate')" 
                        class="px-3 py-1.5 rounded-xl transition cursor-pointer {{ $simulationCategory === 'corporate' ? 'bg-[#0020B2] text-white font-bold shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                    Entreprises (3)
                </button>
            </div>


            <!-- Grille des 20 Scénarios -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 overflow-y-auto custom-scrollbar flex-1 pr-1">
                @foreach($scenarios as $sc)
                <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#0c101a] flex flex-col justify-between space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white font-mono">{{ $sc['name'] }}</span>
                            <p class="text-[11px] text-slate-500 font-mono">{{ $sc['alert_title'] }}</p>
                        </div>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                            {{ $sc['server'] ?? 'srv901529' }}
                        </span>
                    </div>
                    
                    <!-- Boutons d'injection selon la Sévérité souhaitée -->
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 dark:border-slate-800 gap-1.5 text-[11px] font-mono">
                        <span class="text-slate-400 text-[10px]">Simuler :</span>
                        <div class="flex items-center gap-1.5">
                            <button wire:click="triggerScenario('{{ $sc['key'] }}', 'CRITICAL')" 
                                    title="Injecter en Sévérité CRITICAL"
                                    class="px-2.5 py-1 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-[10px] cursor-pointer transition">
                                🔴 Critique
                            </button>
                            <button wire:click="triggerScenario('{{ $sc['key'] }}', 'WARNING')" 
                                    title="Injecter en Sévérité WARNING"
                                    class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-[10px] cursor-pointer transition">
                                🟡 Warning
                            </button>
                            <button wire:click="triggerScenario('{{ $sc['key'] }}', 'INFO')" 
                                    title="Injecter en Sévérité INFO"
                                    class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] cursor-pointer transition">
                                🔵 Info
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>



        </div>
    </div>
    @endif

</div>
