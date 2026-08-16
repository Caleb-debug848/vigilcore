<?php

use Livewire\Component;
use App\Models\Incident;
use App\Services\StatuspageService;

new class extends Component
{
    public string $filter = 'all';
    public ?int $selectedIncidentId = null;
    public ?array $activePayload = null;
    public array $statusComponents = [];

    public function mount(StatuspageService $statuspage)
    {
        $this->loadStatusComponents($statuspage);
    }

    public function loadStatusComponents(StatuspageService $statuspage)
    {
        $this->statusComponents = $statuspage->getComponents();
    }

    public function triggerSimulation(string $type = 'kibana', ?StatuspageService $statuspage = null)
    {
        if ($type === 'kibana') {
            $componentId = 'k8g1fr1p2ptp'; // ID du composant S3P
            $result = null;

            if ($statuspage) {
                $result = $statuspage->createIncident(
                    'HTTP 500 Spike // API S3P Gateway',
                    'investigating',
                    'Détection de 14 erreurs 500 en 60s sur la route /api/v1/payments/process.',
                    $componentId,
                    'major_outage'
                );
            }

            Incident::create([
                'source'                 => 'Kibana',
                'title'                  => 'HTTP 500 Spike // API S3P Gateway',
                'description'            => 'Détection de 14 erreurs 500 en 60s sur la route /api/v1/payments/process.',
                'severity'               => 'critical',
                'status'                 => 'firing',
                'statuspage_incident_id' => $result['id'] ?? null,
                'raw_payload'            => [
                    'host'        => 'srv901529',
                    'service'     => 's3p-gateway',
                    'error_code'  => 500,
                    'statuspage'  => $result ?? null,
                    'trace_id'    => 'trc_' . bin2hex(random_bytes(6)),
                    'timestamp'   => now()->toISOString(),
                ],
            ]);
        } elseif ($type === 'zabbix') {
            Incident::create([
                'source'      => 'Zabbix',
                'title'       => 'High Memory Usage Warning (>88%)',
                'description' => 'La mémoire vive du serveur srv901529 a dépassé 88% d\'occupation.',
                'severity'    => 'warning',
                'status'      => 'firing',
                'raw_payload' => [
                    'host'          => 'srv901529',
                    'metric'        => 'vm.memory.utilization',
                    'current_value' => '88.4%',
                    'threshold'     => '85.0%',
                    'timestamp'     => now()->toISOString(),
                ],
            ]);
        } else {
            Incident::create([
                'source'      => 'n8n Dispatcher',
                'title'       => 'Statuspage Sync & Telegram Notification',
                'description' => 'Orchestration automatique et diffusion du bulletin d\'incident exécutées.',
                'severity'    => 'info',
                'status'      => 'open',
                'raw_payload' => [
                    'channels'     => ['telegram_bot', 'statuspage_api', 'gmail_smtp'],
                    'execution_id' => 'n8n_exec_' . rand(1000, 9999),
                    'timestamp'    => now()->toISOString(),
                ],
            ]);
        }

        if ($statuspage) {
            $this->loadStatusComponents($statuspage);
        }
    }

    public function simulateKibanaIncident(StatuspageService $statuspage)
    {
        $this->triggerSimulation('kibana', $statuspage);
    }

    public function simulateZabbixAlert(StatuspageService $statuspage)
    {
        $this->triggerSimulation('zabbix', $statuspage);
    }

    public function simulateN8nDispatch(StatuspageService $statuspage)
    {
        $this->triggerSimulation('n8n', $statuspage);
    }


    public function resolveIncident($incidentId, StatuspageService $statuspage)
    {
        $incident = Incident::find($incidentId);
        if ($incident) {
            $incident->update(['status' => 'resolved']);

            if (!empty($incident->statuspage_incident_id)) {
                $statuspage->resolveIncident(
                    $incident->statuspage_incident_id,
                    'Incident rétabli par les opérations.'
                );
            }
        }

        if ($this->selectedIncidentId === $incidentId) {
            $this->closePayloadModal();
        }

        $this->loadStatusComponents($statuspage);
    }

    public function inspectPayload($id)
    {
        $incident = Incident::find($id);
        if ($incident) {
            $this->selectedIncidentId = $incident->id;
            $this->activePayload = $incident->raw_payload;
        }
    }

    public function closePayloadModal()
    {
        $this->selectedIncidentId = null;
        $this->activePayload = null;
    }

    public function with(StatuspageService $statuspage)
    {
        $query = Incident::where('status', '!=', 'resolved')->latest();

        if ($this->filter !== 'all') {
            $query->where('severity', $this->filter);
        }

        $incidents = $query->get();
        $total = Incident::where('status', '!=', 'resolved')->count();
        $critical = Incident::where('status', '!=', 'resolved')->where('severity', 'critical')->count();
        $warning = Incident::where('status', '!=', 'resolved')->where('severity', 'warning')->count();
        $info = Incident::where('status', '!=', 'resolved')->where('severity', 'info')->count();

        $hasS3pIncident    = Incident::where('status', '!=', 'resolved')->where(fn($q) => $q->where('title', 'like', '%s3p%')->orWhere('description', 'like', '%s3p%'))->exists();
        $hasDbIncident     = Incident::where('status', '!=', 'resolved')->where(fn($q) => $q->where('title', 'like', '%database%')->orWhere('title', 'like', '%mysql%'))->exists();
        $hasKibanaIncident = Incident::where('status', '!=', 'resolved')->where('source', 'like', '%Kibana%')->exists();
        $hasZabbixIncident = Incident::where('status', '!=', 'resolved')->where('source', 'like', '%Zabbix%')->exists();

        $services = [
            [
                'name'    => 'API S3P Gateway',
                'slug'    => 's3p',
                'status'  => $hasS3pIncident ? 'degraded' : 'operational',
                'latency' => $hasS3pIncident ? '340ms' : '24ms',
                'sla'     => $hasS3pIncident ? '98.20%' : '99.95%',
            ],
            [
                'name'    => 'MySQL Database',
                'slug'    => 'db',
                'status'  => $hasDbIncident ? 'degraded' : 'operational',
                'latency' => $hasDbIncident ? '450ms' : '4ms',
                'sla'     => '99.99%',
            ],
            [
                'name'    => 'Kibana Log Engine',
                'slug'    => 'kibana',
                'status'  => $hasKibanaIncident ? 'warning' : 'operational',
                'latency' => '32ms',
                'sla'     => '99.90%',
            ],
            [
                'name'    => 'Zabbix Collector',
                'slug'    => 'zabbix',
                'status'  => $hasZabbixIncident ? 'warning' : 'operational',
                'latency' => '12ms',
                'sla'     => '99.98%',
            ],
            [
                'name'    => 'n8n Orchestrator',
                'slug'    => 'n8n',
                'status'  => 'operational',
                'latency' => '18ms',
                'sla'     => '100%',
            ],
        ];

        return [
            'incidents'        => $incidents,
            'totalIncidents'   => $total,
            'criticalCount'    => $critical,
            'warningCount'     => $warning,
            'infoCount'        => $info,
            'services'         => $services,
            'statusComponents' => $this->statusComponents,
            'kibanaErrorRate'  => Incident::where('status', '!=', 'resolved')->where('source', 'like', '%Kibana%')->count(),
            'systemHealth'     => $critical > 0 ? 'CRITICAL OUTAGE' : ($warning > 0 ? 'SYSTEM DEGRADED' : 'ALL SYSTEMS NOMINAL'),
        ];
    }
};
?>

<div x-data="{ 
        darkMode: localStorage.getItem('vigilcore_theme') === 'dark' || (!('vigilcore_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        isFlashing: false,
        triggerFlashToggle() {
            if (this.isFlashing) return;
            this.isFlashing = true;
            setTimeout(() => {
                this.darkMode = !this.darkMode;
                localStorage.setItem('vigilcore_theme', this.darkMode ? 'dark' : 'light');
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }, 30);
            setTimeout(() => {
                this.isFlashing = false;
            }, 320);
        }
     }" 
     :class="{ 'dark': darkMode }" 
     class="min-h-screen antialiased relative w-full"
     wire:poll.5s>

    <!-- ==================================================== -->
    <!-- OVERLAY FLASH PHOTO (Xenon Strobe Effect)            -->
    <!-- ==================================================== -->
    <template x-if="isFlashing">
        <div class="fixed inset-0 z-[100] pointer-events-none select-none bg-white animate-camera-flash backdrop-blur-[1px]"></div>
    </template>

    <!-- Fond Global Dynamique avec Padding Progressif -->
    <div class="min-h-screen bg-slate-100 dark:bg-[#0b0f17] text-slate-900 dark:text-slate-100 px-2.5 py-2.5 sm:px-4 sm:py-4 md:px-6 transition-colors duration-200">
        
        <div class="w-full max-w-7xl mx-auto space-y-3 sm:space-y-4">

            <!-- ========================================== -->
            <!-- 1. HEADER STICKY RESPONSIVE                -->
            <!-- ========================================== -->
            <header class="sticky top-1 sm:top-2 z-40 flex items-center justify-between gap-2 p-2.5 sm:p-3 md:px-4 bg-white/90 dark:bg-[#111622]/90 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm dark:shadow-2xl transition-all duration-200">
                
                <!-- Logo & Titre -->
                <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 flex-shrink-0 rounded-lg sm:rounded-xl bg-purple-50 dark:bg-[#161c2e] border border-purple-200/80 dark:border-purple-900/40 p-1 flex items-center justify-center shadow-sm">
                        <svg class="w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M50 15L78 28V52C78 69 66 83 50 88C34 83 22 69 22 52V28L50 15Z" 
                                  class="stroke-purple-600 dark:stroke-purple-400 fill-purple-600/10 dark:fill-purple-500/15" 
                                  stroke-width="6" stroke-linejoin="round"/>
                            <path d="M26 50C33 39 41 33 50 33C59 33 67 39 74 50C67 61 59 67 50 67C41 67 33 61 26 50Z" 
                                  class="stroke-cyan-600 dark:stroke-cyan-400" 
                                  stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="50" cy="50" r="8" class="stroke-cyan-600 dark:stroke-cyan-400" stroke-width="4"/>
                            <path d="M12 50H36L42 41L48 59L54 44L60 54L64 50H88" 
                                  class="stroke-cyan-600 dark:stroke-cyan-400" 
                                  stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="font-extrabold text-sm sm:text-base tracking-tight truncate text-slate-900 dark:text-white">VigilCore</span>
                        <span class="text-[9px] sm:text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60">
                            OPS-01
                        </span>
                    </div>
                </div>

                <!-- Contrôles & Actions -->
                <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">
                    
                    <!-- Badge Live 5s -->
                    <div class="hidden md:flex items-center gap-1.5 text-xs font-mono text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-[#161c2e] px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-800">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-500"></span>
                        </span>
                        <span>Live 5s</span>
                    </div>

                    <!-- Bouton Thème Flash -->
                    <button type="button" 
                            @click="triggerFlashToggle()" 
                            class="flex items-center justify-center p-2 sm:px-2.5 sm:py-1.5 text-xs font-semibold rounded-lg sm:rounded-xl bg-slate-100 dark:bg-[#161c2e] text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 hover:bg-slate-200 dark:hover:bg-slate-800 transition active:scale-95 cursor-pointer">
                        <svg x-show="!darkMode" class="w-4 h-4 sm:w-3.5 sm:h-3.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="darkMode" class="w-4 h-4 sm:w-3.5 sm:h-3.5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <span class="hidden sm:inline ml-1 font-mono font-semibold" x-text="darkMode ? 'Dark' : 'Light'"></span>
                    </button>

                    <!-- Menu Simuler Webhook -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" 
                                type="button"
                                class="relative group overflow-hidden px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-lg sm:rounded-xl text-xs font-bold text-white shadow-md active:scale-95 transition flex items-center gap-1 cursor-pointer">
                            <span class="absolute inset-0 bg-gradient-to-r from-purple-600 via-indigo-600 to-cyan-600"></span>
                            <span class="absolute inset-0 bg-white/15 opacity-0 group-hover:opacity-100 transition"></span>
                            <span class="relative z-10 flex items-center gap-1 font-mono">
                                <svg class="w-3.5 h-3.5 sm:w-3 sm:h-3 text-cyan-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span class="hidden xs:inline">Simuler</span>
                                <span class="hidden sm:inline">Webhook</span>
                            </span>
                        </button>

                        <!-- Menu déroulant responsive -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 sm:w-64 p-1.5 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-2xl z-50 divide-y divide-slate-100 dark:divide-slate-800 font-mono text-xs"
                             style="display: none;">
                            <div class="py-1 space-y-1">
                                <button wire:click="simulateKibanaIncident" @click="open = false" 
                                        class="w-full text-left px-2.5 py-2 text-xs font-medium rounded-lg sm:rounded-xl text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 flex items-center gap-2 transition cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                    <span class="truncate">Crash Kibana (500 S3P)</span>
                                </button>
                                <button wire:click="simulateZabbixAlert" @click="open = false" 
                                        class="w-full text-left px-2.5 py-2 text-xs font-medium rounded-lg sm:rounded-xl text-slate-700 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 flex items-center gap-2 transition cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 flex-shrink-0"></span>
                                    <span class="truncate">Alerte Zabbix (RAM > 88%)</span>
                                </button>
                                <button wire:click="simulateN8nDispatch" @click="open = false" 
                                        class="w-full text-left px-2.5 py-2 text-xs font-medium rounded-lg sm:rounded-xl text-slate-700 dark:text-slate-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/30 flex items-center gap-2 transition cursor-pointer">
                                    <span class="w-2 h-2 rounded-full bg-purple-500 flex-shrink-0"></span>
                                    <span class="truncate">Webhook n8n Dispatch</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- ========================================== -->
            <!-- 2. KPI TOP METRICS (1 col -> 2 col -> 4 col) -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                
                <!-- KPI 1 -->
                <div class="dash-card p-3 sm:p-3.5 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-[10px] sm:text-[11px] font-mono font-bold uppercase">Total Tracked</span>
                        <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-xl sm:text-2xl font-black font-mono text-slate-900 dark:text-white">{{ $totalIncidents }}</span>
                        <span class="text-[9px] sm:text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-semibold">100% Ingest</span>
                    </div>
                </div>

                <!-- KPI 2 -->
                <div class="dash-card p-3 sm:p-3.5 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] sm:text-[11px] font-mono font-bold uppercase text-red-600 dark:text-red-400 flex items-center gap-1.5 truncate">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
                            Brèches Critiques
                        </span>
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-xl sm:text-2xl font-black font-mono text-red-600 dark:text-red-500">{{ $criticalCount }}</span>
                        <span class="text-[9px] sm:text-[10px] font-mono font-semibold {{ $criticalCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ $criticalCount > 0 ? 'Action Requise' : 'Nominal' }}
                        </span>
                    </div>
                </div>

                <!-- KPI 3 -->
                <div class="dash-card p-3 sm:p-3.5 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-[10px] sm:text-[11px] font-mono font-bold uppercase">Kibana Errors</span>
                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-xl sm:text-2xl font-black font-mono text-slate-900 dark:text-white">{{ $kibanaErrorRate }}<span class="text-xs font-normal text-slate-400">/min</span></span>
                        <span class="text-[9px] sm:text-[10px] font-mono text-slate-500 dark:text-slate-400 font-semibold">srv901529</span>
                    </div>
                </div>

                <!-- KPI 4 -->
                <div class="dash-card p-3 sm:p-3.5 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                        <span class="text-[10px] sm:text-[11px] font-mono font-bold uppercase">n8n Dispatcher</span>
                        <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between">
                        <span class="text-lg sm:text-xl font-black font-mono text-cyan-600 dark:text-cyan-400">Active</span>
                        <span class="text-[9px] sm:text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-semibold">24/7 Live</span>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- 3. SERVICE MATRIX & SEVERITY (Stack -> Grid) -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4 items-stretch">
                
                <!-- Service Health Matrix (2 colonnes sur Desktop) -->
                <div class="dash-card lg:col-span-2 p-3 sm:p-4 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 dark:border-slate-800/80">
                            <div class="min-w-0 pr-2">
                                <h3 class="text-xs font-mono font-bold uppercase text-slate-800 dark:text-slate-200 truncate">Service Health Matrix</h3>
                                <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">Surveillance temps réel des composants</p>
                            </div>
                            <span class="text-[9px] sm:text-[10px] font-mono px-2 py-0.5 rounded-md bg-slate-100 dark:bg-[#161c2e] text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 flex-shrink-0 font-semibold">
                                srv901529
                            </span>
                        </div>

                        <!-- Grille responsive des cartes composants avec scrollbar fluide -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-2.5 mt-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                            @if(!empty($statusComponents))
                                @foreach($statusComponents as $component)
                                    @if(($component['group'] ?? false) === false && ($component['showcase'] ?? true) === true)
                                        <div class="p-2 sm:p-2.5 bg-slate-50 dark:bg-[#0d111a] border border-slate-200 dark:border-slate-800/80 rounded-lg sm:rounded-xl flex items-center justify-between hover:border-purple-300 dark:hover:border-purple-800/60 transition min-w-0 shadow-xs">
                                            <div class="min-w-0 pr-2">
                                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" title="{{ $component['name'] }}">
                                                    {{ $component['name'] }}
                                                </h4>
                                                <span class="text-[9px] font-mono uppercase {{ ($component['status'] ?? '') === 'major_outage' ? 'text-red-500' : (($component['status'] ?? '') === 'operational' ? 'text-slate-500 dark:text-slate-400' : 'text-amber-500') }}">
                                                    {{ str_replace('_', ' ', $component['status'] ?? 'operational') }}
                                                </span>
                                            </div>
                                            <span class="w-2 h-2 rounded-full flex-shrink-0 
                                                @if(($component['status'] ?? '') === 'operational') bg-emerald-500 shadow-sm shadow-emerald-500/50
                                                @elseif(($component['status'] ?? '') === 'major_outage') bg-red-500 shadow-sm shadow-red-500/50 animate-pulse
                                                @elseif(($component['status'] ?? '') === 'partial_outage') bg-orange-500 shadow-sm
                                                @else bg-amber-500 shadow-sm @endif">
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                @foreach($services as $svc)
                                    <div class="p-2 sm:p-2.5 bg-slate-50 dark:bg-[#0d111a] border border-slate-200 dark:border-slate-800/80 rounded-lg sm:rounded-xl flex items-center justify-between hover:border-purple-300 dark:hover:border-purple-800/60 transition min-w-0 shadow-xs">
                                        <div class="min-w-0 pr-2">
                                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">
                                                {{ $svc['name'] }}
                                            </h4>
                                            <span class="text-[9px] font-mono uppercase {{ $svc['status'] === 'degraded' ? 'text-red-500' : 'text-slate-500 dark:text-slate-400' }}">
                                                {{ $svc['status'] }} • {{ $svc['latency'] }}
                                            </span>
                                        </div>
                                        <span class="w-2 h-2 rounded-full flex-shrink-0 
                                            @if($svc['status'] === 'operational') bg-emerald-500 shadow-sm shadow-emerald-500/50
                                            @elseif($svc['status'] === 'degraded') bg-red-500 shadow-sm shadow-red-500/50 animate-pulse
                                            @else bg-amber-500 shadow-sm @endif">
                                        </span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px] font-mono text-slate-500">
                        <span>Sonde Statuspage Atlassian active</span>
                        <span class="text-purple-600 dark:text-purple-400 font-semibold">{{ !empty($statusComponents) ? count($statusComponents) . ' Composants' : '5 Nœuds Locaux' }}</span>
                    </div>
                </div>

                <!-- Severity Breakdown -->
                <div class="dash-card p-3 sm:p-4 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800/80">
                        <span class="text-xs font-mono font-bold uppercase text-slate-800 dark:text-slate-200">Severity Breakdown</span>
                        <span class="text-[9px] sm:text-[10px] font-mono px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-950/70 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60 font-semibold">
                            Ratio Actif
                        </span>
                    </div>

                    @php
                        $circ = 289.02;
                        $critLen = $totalIncidents > 0 ? ($criticalCount / $totalIncidents) * $circ : 0;
                        $warnLen = $totalIncidents > 0 ? ($warningCount / $totalIncidents) * $circ : 0;
                        $infoLen = $totalIncidents > 0 ? ($infoCount / $totalIncidents) * $circ : 0;
                        $critPct = $totalIncidents > 0 ? round(($criticalCount / $totalIncidents) * 100) : 0;
                        $warnPct = $totalIncidents > 0 ? round(($warningCount / $totalIncidents) * 100) : 0;
                        $infoPct = $totalIncidents > 0 ? round(($infoCount / $totalIncidents) * 100) : 0;
                    @endphp

                    <!-- Donut Chart -->
                    <div class="relative flex items-center justify-center my-3">
                        <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="46" stroke="currentColor" stroke-width="10" fill="transparent" class="text-slate-100 dark:text-[#182030]" />
                            @if($criticalCount > 0)
                                <circle cx="60" cy="60" r="46" stroke="#ef4444" stroke-width="10" fill="transparent" stroke-dasharray="{{ $critLen }} {{ $circ }}" stroke-dashoffset="0" stroke-linecap="round" class="drop-shadow-[0_0_6px_rgba(239,68,68,0.35)] transition-all duration-500" />
                            @endif
                            @if($warningCount > 0)
                                <circle cx="60" cy="60" r="46" stroke="#f59e0b" stroke-width="10" fill="transparent" stroke-dasharray="{{ $warnLen }} {{ $circ }}" stroke-dashoffset="-{{ $critLen }}" stroke-linecap="round" class="drop-shadow-[0_0_6px_rgba(245,158,11,0.35)] transition-all duration-500" />
                            @endif
                            @if($infoCount > 0)
                                <circle cx="60" cy="60" r="46" stroke="#8b5cf6" stroke-width="10" fill="transparent" stroke-dasharray="{{ $infoLen }} {{ $circ }}" stroke-dashoffset="-{{ $critLen + $warnLen }}" stroke-linecap="round" class="drop-shadow-[0_0_6px_rgba(139,92,246,0.35)] transition-all duration-500" />
                            @endif
                        </svg>
                        <div class="absolute flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-xl font-black font-mono text-slate-900 dark:text-white">{{ $totalIncidents }}</span>
                            <span class="text-[8px] font-mono uppercase text-slate-400 dark:text-slate-500 font-bold">Actifs</span>
                        </div>
                    </div>

                    <!-- Pills -->
                    <div class="space-y-1.5 mb-2 font-mono">
                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/40">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
                                <span class="text-xs font-semibold text-red-950 dark:text-slate-200 truncate">Critique</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-red-600 dark:text-red-400 flex-shrink-0">{{ $criticalCount }} <span class="text-[9px] text-slate-400 font-normal">({{ $critPct }}%)</span></span>
                        </div>
                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/40">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                                <span class="text-xs font-semibold text-amber-950 dark:text-slate-200 truncate">Warning</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 flex-shrink-0">{{ $warningCount }} <span class="text-[9px] text-slate-400 font-normal">({{ $warnPct }}%)</span></span>
                        </div>
                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-[#0d111a] border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500 flex-shrink-0"></span>
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 truncate">Info</span>
                            </div>
                            <span class="text-xs font-mono font-bold text-slate-400 flex-shrink-0">{{ $infoCount }} <span class="text-[9px] text-slate-400 font-normal">({{ $infoPct }}%)</span></span>
                        </div>
                    </div>

                    <!-- Footer Statuspage -->
                    <div class="py-1.5 px-2 bg-slate-50 dark:bg-[#0d111a] border border-slate-200 dark:border-slate-800 rounded-lg flex items-center justify-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400 font-mono">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500/50 flex-shrink-0"></span>
                        <span class="truncate">Sync <a href="https://opsca.statuspage.io" target="_blank" class="text-purple-600 dark:text-purple-400 hover:underline font-semibold">Statuspage</a></span>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 4. FLUX DES INCIDENTS (Mobile First Stack) -->
            <!-- ========================================== -->
            <div class="dash-card p-3 sm:p-4 bg-white dark:bg-[#111622] border border-slate-200 dark:border-slate-800 rounded-xl sm:rounded-2xl shadow-sm">
                
                <!-- En-tête & Filtres horizontaux sans débordement -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <div class="min-w-0">
                        <h3 class="text-xs font-mono font-bold uppercase text-slate-800 dark:text-slate-200 truncate">Flux des Incidents & Triage Direct</h3>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 truncate">Payloads JSON routés depuis Kibana, Zabbix et n8n</p>
                    </div>
                    
                    <!-- Boutons filtres -->
                    <div class="flex items-center gap-1 text-xs font-mono overflow-x-auto pb-1 sm:pb-0">
                        <button wire:click="$set('filter', 'all')" 
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $filter === 'all' ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 shadow-sm' : 'bg-slate-100 dark:bg-[#161c2e] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                            Tous ({{ $totalIncidents }})
                        </button>
                        <button wire:click="$set('filter', 'critical')" 
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $filter === 'critical' ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-[#161c2e] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                            Critiques ({{ $criticalCount }})
                        </button>
                        <button wire:click="$set('filter', 'warning')" 
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer {{ $filter === 'warning' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-[#161c2e] text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-800' }}">
                            Warnings ({{ $warningCount }})
                        </button>
                    </div>
                </div>

                <!-- Liste des Incidents (Disposition fluide adaptée aux mobiles) -->
                <div class="divide-y divide-slate-100 dark:divide-slate-800/80 mt-1">
                    @forelse($incidents as $incident)
                        @php
                            $severity = strtolower($incident->severity ?? 'info');
                            $badgeClass = match($severity) {
                                'critical' => 'bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 border-red-200 dark:border-red-900/60',
                                'warning'  => 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-900/60',
                                default    => 'bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-900/60',
                            };
                        @endphp
                        <div class="py-3 flex flex-col md:flex-row md:items-center justify-between gap-2.5 hover:bg-slate-50/60 dark:hover:bg-[#0e131d]/60 px-2 rounded-xl transition">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <span class="text-[9px] sm:text-[10px] font-mono font-bold px-2 py-0.5 rounded border flex-shrink-0 mt-0.5 {{ $badgeClass }}">
                                    {{ strtoupper($incident->severity ?? 'INFO') }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="text-xs font-bold text-slate-800 dark:text-slate-100 break-words">
                                            {{ $incident->title }}
                                        </span>
                                        <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-slate-100 dark:bg-[#161c2e] text-slate-500 dark:text-slate-400">
                                            {{ $incident->source }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 break-words">
                                        {{ $incident->description }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Actions & Timing -->
                            <div class="flex items-center justify-between md:justify-end gap-2 text-xs font-mono pt-1 md:pt-0 border-t border-slate-50 dark:border-slate-800/40 md:border-0">
                                <span class="text-slate-400 text-[10px] sm:text-[11px] whitespace-nowrap"
                                      x-data="{ 
                                          created: new Date('{{ $incident->created_at->toIso8601String() }}'),
                                          timeAgo: '',
                                          update() {
                                              const diff = Math.max(0, Math.floor((new Date() - this.created) / 1000));
                                              if (diff < 60) {
                                                  this.timeAgo = 'il y a ' + diff + ' seconde' + (diff > 1 ? 's' : '');
                                              } else if (diff < 3600) {
                                                  const mins = Math.floor(diff / 60);
                                                  this.timeAgo = 'il y a ' + mins + ' min';
                                              } else {
                                                  const hours = Math.floor(diff / 3600);
                                                  this.timeAgo = 'il y a ' + hours + ' h';
                                              }
                                          }
                                      }" 
                                      x-init="update(); setInterval(() => update(), 1000)" 
                                      x-text="timeAgo">
                                    {{ $incident->created_at->diffForHumans() }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <button wire:click="inspectPayload({{ $incident->id }})" 
                                            class="px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-[#161c2e] hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-semibold cursor-pointer transition">
                                        JSON
                                    </button>
                                    <button wire:click="resolveIncident({{ $incident->id }})" 
                                            class="px-3 py-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 hover:bg-purple-100 dark:hover:bg-purple-900/60 border border-purple-200 dark:border-purple-800 text-xs font-semibold cursor-pointer transition">
                                        Résoudre
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs font-mono text-slate-500">
                            <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-1.5 font-bold">
                                ✓
                            </div>
                            Aucun incident actif
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Visualiseur JSON -->
    @if($selectedIncidentId && $activePayload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
             wire:click.self="closePayloadModal">
            <div class="w-full max-w-2xl rounded-2xl border p-5 shadow-2xl transition-all font-mono bg-white dark:bg-[#111622] border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100">
                <div class="flex justify-between items-center pb-3 border-b border-slate-100 dark:border-slate-800 mb-3">
                    <h3 class="text-xs font-bold flex items-center gap-2 text-purple-600 dark:text-purple-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        Telemetry Raw Payload (ID #{{ $selectedIncidentId }})
                    </h3>
                    <button wire:click="closePayloadModal" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-base font-bold cursor-pointer">&times;</button>
                </div>
                <pre class="p-4 rounded-xl text-xs overflow-x-auto border leading-relaxed bg-[#0b0f17] border-slate-800 text-emerald-400 max-h-96">{{ json_encode($activePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                <div class="mt-3 flex justify-end">
                    <button wire:click="closePayloadModal" class="px-4 py-1.5 rounded-lg text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white transition-all cursor-pointer">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>