<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion — VigilCore OPS-01</title>

    <!-- Favicon Officiel -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Styles & Scripts Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#060810] text-slate-100 font-sans antialiased min-h-screen selection:bg-purple-500 selection:text-white relative overflow-x-hidden flex items-center justify-center p-3 sm:p-6 lg:p-10">

    <!-- ==================================================== -->
    <!-- EFFETS DE FOND & LUEURS AMBIANTES (VIGILCORE GLOW)  -->
    <!-- ==================================================== -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-600/20 rounded-full blur-[128px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-cyan-500/20 rounded-full blur-[128px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[140px]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-indigo-950/20 via-[#060810]/80 to-[#060810]"></div>
    </div>

    <!-- ==================================================== -->
    <!-- CONTENEUR PRINCIPAL SPLIT-SCREEN DESKTOP & MOBILE    -->
    <!-- ==================================================== -->
    <div class="relative z-10 w-full max-w-5xl rounded-3xl bg-[#0d1322]/80 backdrop-blur-2xl border border-slate-800/90 shadow-2xl shadow-purple-950/30 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[600px]">

        <!-- ==================================================== -->
        <!-- COLONNE GAUCHE : IDENTITÉ VISUELLE & OPS BRANDING    -->
        <!-- (Visible sur Desktop / Tablettes, épurée sur Mobile)  -->
        <!-- ==================================================== -->
        <div class="lg:col-span-6 relative p-6 sm:p-10 lg:p-12 flex flex-col justify-between overflow-hidden bg-gradient-to-b from-[#0e1628] to-[#090d16] border-b lg:border-b-0 lg:border-r border-slate-800/80">
            
            <!-- Décoration d'arrière-plan tech -->
            <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-gradient-to-tr from-purple-600/20 via-indigo-600/20 to-cyan-500/20 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Header de marque -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-2xl overflow-hidden shadow-lg shadow-indigo-500/20 border border-purple-500/40 p-0.5 bg-gradient-to-tr from-purple-600 to-cyan-400">
                        <img src="{{ asset('images/logo.png') }}" alt="VigilCore Logo" class="h-full w-full object-cover rounded-[14px]">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-xl tracking-tight text-white">VigilCore</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-950/80 text-purple-300 border border-purple-800/80">OPS-01</span>
                        </div>
                        <p class="text-xs font-mono text-cyan-400">Incident & SLA Command Center</p>
                    </div>
                </div>

                <div class="pt-4 space-y-2">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                        Supervision en Temps Réel & Surveillance SLA
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed font-sans">
                        Accédez à la console d'exploitation opérationnelle pour monitorer vos flux Kibana, Zabbix, vos pipelines n8n et gérer les incidents critiques en production.
                    </p>
                </div>
            </div>

            <!-- Piliers de sécurité & monitoring -->
            <div class="hidden sm:grid grid-cols-1 gap-2.5 my-6">
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/70 text-xs font-mono text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-xs shadow-emerald-400 animate-pulse"></span>
                    <span>Monitoring Automatisé 24/7 & Alertes Push</span>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/70 text-xs font-mono text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                    <span>Calcul & Conformité SLA Instantanés (≥ 99.5%)</span>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/70 text-xs font-mono text-slate-300">
                    <span class="h-2 w-2 rounded-full bg-purple-400"></span>
                    <span>Synchronisation Statuspage & Traçabilité d'Audit</span>
                </div>
            </div>

            <!-- Footer gauche : Métadonnées du serveur -->
            <div class="pt-4 border-t border-slate-800/60 flex items-center justify-between text-[11px] font-mono text-slate-500">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span> Host : srv901529
                </span>
                <span class="text-slate-400">v1.0.0 — Production</span>
            </div>

        </div>

        <!-- ==================================================== -->
        <!-- COLONNE DROITE : FORMULAIRE DE CONNEXION SÉCURISÉ     -->
        <!-- ==================================================== -->
        <div class="lg:col-span-6 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-[#0d1322]/95 relative"
             x-data="{ showPassword: false }">

            <div class="w-full max-w-sm mx-auto space-y-6">

                <!-- En-tête formulaire -->
                <div class="space-y-1.5">
                    <h3 class="text-xl sm:text-2xl font-bold font-mono text-white tracking-tight flex items-center gap-2">
                        <span>Authentification</span>
                        <span class="inline-block w-2 h-2 rounded-full bg-purple-500"></span>
                    </h3>
                    <p class="text-xs text-slate-400 font-mono">
                        Veuillez saisir vos identifiants d'accès sécurisés.
                    </p>
                </div>

                <!-- Statut de session Laravel Breeze -->
                @if (session('status'))
                    <div class="p-3 rounded-xl bg-emerald-950/60 border border-emerald-800/80 text-emerald-400 text-xs font-mono">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Formulaire POST -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Champ Adresse Email -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-mono font-semibold text-slate-300">
                            Identifiant / Email Professionnel
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username"
                                   placeholder="ops@vigilcore.internal"
                                   class="w-full pl-10 pr-3.5 py-2.5 rounded-xl bg-[#070a12] border @error('email') border-rose-500 text-rose-300 @else border-slate-700/80 text-slate-100 @enderror text-xs font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition shadow-inner">
                        </div>
                        @error('email')
                            <p class="text-[11px] font-mono text-rose-400 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Champ Mot de Passe avec Toggle Visibilité -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-mono font-semibold text-slate-300">
                                Clé d'Accès / Mot de Passe
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[11px] font-mono text-purple-400 hover:text-purple-300 hover:underline transition">
                                    Clé oubliée ?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   placeholder="••••••••••••"
                                   class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-[#070a12] border @error('password') border-rose-500 text-rose-300 @else border-slate-700/80 text-slate-100 @enderror text-xs font-mono placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition shadow-inner">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 cursor-pointer transition">
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
                            <p class="text-[11px] font-mono text-rose-400 mt-1 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Mémoriser la session -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember" 
                                   class="h-4 w-4 rounded bg-[#070a12] border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-[#0d1322] cursor-pointer">
                            <span class="text-xs font-mono text-slate-400 hover:text-slate-300 transition">
                                Maintenir la session active
                            </span>
                        </label>
                    </div>

                    <!-- Bouton de Connexion Principal -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="btn-interactive w-full py-3 px-4 rounded-xl text-white font-mono font-bold text-xs uppercase tracking-wider bg-gradient-to-r from-purple-600 via-indigo-600 to-cyan-500 hover:opacity-95 active:scale-[0.98] shadow-lg shadow-indigo-600/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-cyan-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Déverrouiller la Console OPS</span>
                        </button>
                    </div>
                </form>

                <!-- Avertissement de sécurité & restriction d'accès -->
                <div class="pt-4 border-t border-slate-800/80 text-center">
                    <p class="text-[10px] font-mono text-slate-500 leading-relaxed">
                        🔒 Système de supervision interne confidentiel.<br>
                        Toute tentative d'accès non autorisée est journalisée et tracée.
                    </p>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
