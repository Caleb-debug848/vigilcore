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
                    <img src="{{ asset('images/logo.png') }}" style="width:38px; height:38px; border-radius:8px; object-fit:cover; border:1px solid #4f46e5;">
                    <div>
                        <h1>VIGILCORE OPS-01 — RAPPORT D'AUDIT & ANALYTICS SLA</h1>
                        <p>Plateforme de Supervision, Surveillance des Services & Corrélation d'Alertes</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span style="background-color:#059669; color:#ffffff; font-weight:bold; font-size:9pt; padding:4px 10px; border-radius:6px; font-family:monospace;">
                        SLA : {{ $uptimePct }}% CONFORME
                    </span>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:8pt; color:#94a3b8; padding-top:6px; font-family:monospace;">
                <span>Période analysée : <b style="color:#ffffff;">{{ strtoupper($period) }}</b></span>
                <span>Date d'extraction : <b style="color:#ffffff;">{{ now()->format('d/m/Y à H:i:s') }}</b></span>
                <span>Hôte : <b style="color:#ffffff;">srv901529 (Production)</b></span>
            </div>
        </div>

        <!-- ==================================================== -->
        <!-- 1. EN-TÊTE PRINCIPAL NAVIGATION (STICKY PERSISTANT) -->
        <!-- ==================================================== -->
        <header class="no-print animate-entrance sticky top-2 sm:top-3 z-40 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4 p-3 sm:p-3.5 md:px-4 rounded-xl sm:rounded-2xl bg-white/85 dark:bg-[#0d1322]/85 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800 shadow-sm dark:shadow-2xl transition-all duration-200">

            <!-- Logo, Titre & Badge OPS-01 -->
            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-start">
                <a href="/dashboard" class="flex items-center gap-2.5 group">
                    <div class="h-9 w-9 rounded-xl overflow-hidden shadow-xs group-hover:scale-105 transition-transform flex items-center justify-center border border-purple-500/30">
                        <img src="{{ asset('images/logo.png') }}" alt="VigilCore" class="h-full w-full object-cover">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-base tracking-tight text-slate-900 dark:text-white">VigilCore</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">OPS-01</span>
                        </div>
                        <p class="text-[10px] font-mono text-slate-400 dark:text-slate-500">Analytics & SLA Engine</p>
                    </div>
                </a>
            </div>


            <!-- Contrôles du Header -->
            <div class="flex items-center gap-2 sm:gap-2.5 flex-wrap justify-end">
                <!-- Pillule Statut SLA Engine -->
                <div class="px-3 py-1.5 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800/60 text-cyan-700 dark:text-cyan-400 text-xs font-mono font-bold flex items-center gap-1.5 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-cyan-500 animate-pulse"></span>
                    <span>SLA Engine</span>
                </div>

                <!-- Toggle Thème Dark / Light -->
                <button @click="darkMode = !darkMode" type="button" class="btn-interactive px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-[#161c2e] hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-mono font-semibold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-amber-500" x-show="!darkMode" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg class="w-3.5 h-3.5 text-purple-400" x-show="darkMode" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="font-mono font-semibold" x-text="darkMode ? 'Dark' : 'Light'"></span>
                </button>

                <!-- Retour Supervision Monitoring Live -->
                <a href="/dashboard" class="btn-interactive px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-[#161c2e] hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Monitoring Live</span>
                </a>

                <!-- Export Excel Stylé -->
                <button wire:click="exportExcel" class="btn-interactive px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-mono font-bold flex items-center gap-1.5 transition cursor-pointer shadow-2xs">
                    <svg class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Export Excel (.xls)</span>
                </button>

                <!-- Bouton Imprimer / PDF -->
                <button onclick="window.print()" class="btn-interactive px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-purple-600 via-indigo-600 to-cyan-600 hover:opacity-90 text-white text-xs font-mono font-bold flex items-center gap-1.5 transition shadow-sm cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-cyan-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656" />
                    </svg>
                    <span>Imprimer / PDF</span>
                </button>

                @auth
                <!-- Bouton Déconnexion Authentifié -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" title="Se déconnecter ({{ auth()->user()->name }})" class="btn-interactive p-2 sm:px-2.5 sm:py-1.5 text-xs font-semibold rounded-lg sm:rounded-xl bg-slate-100 dark:bg-[#161c2e] text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-slate-200 dark:border-slate-800 transition flex items-center gap-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        <span class="hidden md:inline font-mono">Quitter</span>
                    </button>
                </form>
                @endauth
            </div>
        </header>


        <!-- ==================================================== -->
        <!-- 2. CARTES KPIS SUPÉRIEURES (EXCEL / SCREEN STYLÉES)  -->
        <!-- ==================================================== -->
        <div class="animate-entrance stagger-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- TOTAL INCIDENTS -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider">TOTAL INCIDENTS</span>
                    <span class="no-print p-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="kpi-total-val text-3xl font-extrabold font-mono text-slate-900 dark:text-white">{{ $totalCount }}</span>
                    <span class="badge-res px-2 py-0.5 rounded text-[10px] font-mono font-bold">{{ $resolvedCount }} Clôturés</span>
                </div>
                <div class="no-print w-full bg-slate-100 dark:bg-slate-800/60 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full" style="width: {{ $resolutionRate }}%"></div>
                </div>
            </div>

            <!-- DISPONIBILITÉ GLOBALE (SLA) -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-slate-500 dark:text-slate-400 tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> DISPONIBILITÉ SLA
                    </span>
                    <span class="badge-res px-2 py-0.5 rounded text-[10px] font-mono font-bold">Target ≥ 99.5%</span>
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
                    <span class="text-[10px] font-mono font-bold uppercase text-amber-500 tracking-wider">MTTR MOYEN</span>
                    <span class="no-print p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-500 border border-amber-200 dark:border-amber-800/50">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="kpi-mttr-val text-3xl font-extrabold font-mono text-amber-500 dark:text-amber-400">{{ $mttrFormatted }}</span>
                    <span class="text-[11px] font-mono text-slate-400">srv901529</span>
                </div>
                <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400 truncate">
                    Temps moyen de rétablissement
                </div>
            </div>

            <!-- N8N PIPELINE AUTOMATION -->
            <div class="dash-card print-kpi-card p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#0d1322] border border-slate-200 dark:border-slate-800/90 shadow-sm space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-mono font-bold uppercase text-cyan-600 dark:text-cyan-400 tracking-wider">N8N PIPELINE</span>
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
                            TOP SERVICES IMPACTÉS & STABILITÉ
                        </h3>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Volume de pannes et fréquence par composant</p>
                    </div>
                    <span class="badge-info px-2 py-0.5 rounded text-[10px] font-mono font-bold">
                        Top 5
                    </span>
                </div>

                @if($topServices->isEmpty())
                    <div class="py-8 text-center text-xs font-mono text-slate-400">
                        Aucun incident enregistré sur la période. Tous les services sont opérationnels.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($topServices as $service)
                            <div class="service-row-box p-3 rounded-xl bg-slate-50 dark:bg-[#090e1a] border border-slate-200/80 dark:border-slate-800/80 space-y-1.5">
                                <div class="flex items-center justify-between text-xs font-mono">
                                    <span class="font-bold text-slate-900 dark:text-white truncate pr-2">{{ $service['name'] }}</span>
                                    <span class="text-slate-600 dark:text-slate-400 font-semibold flex-shrink-0">{{ $service['count'] }} alerte(s) ({{ $service['pct'] }}%)</span>
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
                        RÉPARTITION PAR CRITICITÉ
                    </h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Classification opérationnelle des alertes</p>
                </div>

                <div class="space-y-2.5">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/40">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <span class="badge-crit text-xs font-mono font-bold">CRITICAL</span>
                        </div>
                        <span class="text-sm font-mono font-extrabold text-rose-600 dark:text-rose-400">{{ $criticalCount }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                            <span class="badge-warn text-xs font-mono font-bold">WARNING</span>
                        </div>
                        <span class="text-sm font-mono font-extrabold text-amber-600 dark:text-amber-400">{{ $warningCount }}</span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                            <span class="badge-info text-xs font-mono font-bold">INFO</span>
                        </div>
                        <span class="text-sm font-mono font-extrabold text-blue-600 dark:text-blue-400">{{ $infoCount }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 text-[11px] font-mono text-slate-500 dark:text-slate-400">
                    Moteur de Corrélation : <b>Kibana Logs + Zabbix + n8n</b>
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
                        REGISTRE D'AUDIT & TRAÇABILITÉ DES ÉVÉNEMENTS
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                        Historique horodaté et durée exacte de chaque panne
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Sélecteur Période en Pilules -->
                    <div class="flex items-center gap-1 bg-slate-200/80 dark:bg-[#070b14] p-1 rounded-xl border border-slate-300 dark:border-slate-800 text-xs font-mono">
                        <button wire:click="setPeriod('today')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'today' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            Aujourd'hui
                        </button>
                        <button wire:click="setPeriod('week')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'week' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            7 Jours
                        </button>
                        <button wire:click="setPeriod('month')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'month' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            30 Jours
                        </button>
                        <button wire:click="setPeriod('all')" class="btn-interactive px-2.5 py-1 rounded-lg transition font-bold cursor-pointer {{ $period === 'all' ? 'bg-slate-900 dark:bg-[#161c2e] text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                            Tout
                        </button>
                    </div>

                    <!-- Champ Recherche -->
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Filtrer un composant..." class="px-2.5 py-1.5 rounded-xl bg-white dark:bg-[#070b14] border border-slate-200 dark:border-slate-700/80 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-indigo-500 font-mono">

                    <!-- Filtre Sévérité -->
                    <select wire:model.live="severityFilter" class="px-2.5 py-1.5 rounded-xl bg-white dark:bg-[#070b14] border border-slate-200 dark:border-slate-700/80 text-xs text-slate-700 dark:text-slate-300 focus:outline-none focus:border-indigo-500 font-mono cursor-pointer">
                        <option value="all">Toutes Sévérités</option>
                        <option value="critical">Critiques</option>
                        <option value="warning">Warnings</option>
                        <option value="info">Info</option>
                    </select>
                </div>
            </div>

            <!-- Contenu Table (Aligné Strictement sur Excel) -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-[#4338ca] text-white uppercase text-[10px] tracking-wider border-b border-indigo-900">
                        <tr>
                            <th class="py-3 px-4" style="width:65px;">ID</th>
                            <th class="py-3 px-4" style="width:160px;">HORODATAGE</th>
                            <th class="py-3 px-4">COMPOSANT / ALERTE</th>
                            <th class="py-3 px-4 text-center" style="width:110px;">SÉVÉRITÉ</th>
                            <th class="py-3 px-4 text-center" style="width:110px;">STATUT</th>
                            <th class="py-3 px-4 text-center" style="width:110px;">DURÉE (MTTR)</th>
                            <th class="py-3 px-4" style="width:160px;">SOURCE</th>
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
                                <td class="py-3 px-4 font-bold text-slate-500 dark:text-slate-400">#{{ $incident->id }}</td>
                                <td class="py-3 px-4 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                    {{ $incident->created_at ? $incident->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900 dark:text-white max-w-xs truncate">
                                    {{ $incident->title }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if(strtoupper($incident->severity) === 'CRITICAL')
                                        <span class="badge-crit">CRITICAL</span>
                                    @elseif(strtoupper($incident->severity) === 'WARNING')
                                        <span class="badge-warn">WARNING</span>
                                    @else
                                        <span class="badge-info">INFO</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($incident->status === 'resolved')
                                        <span class="badge-res">RÉSOLU</span>
                                    @else
                                        <span class="badge-open">EN COURS</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-bold text-amber-600 dark:text-amber-400 text-center">
                                    {{ $mttr }}
                                </td>
                                <td class="py-3 px-4 text-[11px] text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $incident->source ?? 'Kibana Logs Engine' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 font-mono text-xs">
                                    Aucun incident trouvé sur cette période.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
                <span>🛡️ <b>VigilCore OPS-01</b> — Rapport d'exploitation et conformité SLA généré automatiquement</span>
                <span>Document strictement confidentiel — Usage interne</span>
            </div>
        </div>

    </div>
</div>
