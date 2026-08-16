<?php

use Livewire\Component;
use App\Models\Incident;

new class extends Component
{
    public string $filter = 'all';
    public ?int $selectedIncidentId = null;
    public ?array $activePayload = null;

    public function triggerSimulation(string $type = 'kibana')
    {
        if ($type === 'kibana') {
            Incident::create([
                'source'      => 'Kibana Logs Engine',
                'title'       => 'HTTP 500 Spike // API S3P Gateway',
                'description' => 'Détection de 14 erreurs 500 en 60s sur la route /api/v1/payments/process.',
                'severity'    => 'critical',
                'status'      => 'firing',
                'raw_payload' => [
                    'host' => 'srv901529',
                    'service' => 's3p-gateway',
                    'error_code' => 500,
                    'trace_id' => 'trc_' . bin2hex(random_bytes(6)),
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        } elseif ($type === 'zabbix') {
            Incident::create([
                'source'      => 'Zabbix Agent 2',
                'title'       => 'High Memory Usage Warning (>88%)',
                'description' => 'Le conteneur Elasticsearch srv901529_es_1 approche du seuil critique de RAM.',
                'severity'    => 'warning',
                'status'      => 'firing',
                'raw_payload' => [
                    'host' => 'srv901529',
                    'metric' => 'vm.memory.utilization',
                    'current_value' => '88.4%',
                    'threshold' => '85.0%',
                    'timestamp' => now()->toISOString(),
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
                    'channels' => ['telegram_bot', 'statuspage_api', 'gmail_smtp'],
                    'execution_id' => 'n8n_exec_' . rand(1000, 9999),
                    'timestamp' => now()->toISOString(),
                ],
            ]);
        }
    }

    public function resolveIncident($id)
    {
        $incident = Incident::find($id);
        if ($incident) {
            $incident->delete();
        }
        if ($this->selectedIncidentId === $id) {
            $this->closePayloadModal();
        }
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

    public function with()
    {
        $query = Incident::latest();

        if ($this->filter !== 'all') {
            $query->where('severity', $this->filter);
        }

        $incidents = $query->get();
        $total = Incident::count();
        $critical = Incident::where('severity', 'critical')->count();
        $warning = Incident::where('severity', 'warning')->count();
        $info = Incident::where('severity', 'info')->count();

        $hasS3pIncident    = Incident::where(fn($q) => $q->where('title', 'like', '%s3p%')->orWhere('description', 'like', '%s3p%'))->exists();
        $hasDbIncident     = Incident::where(fn($q) => $q->where('title', 'like', '%database%')->orWhere('title', 'like', '%mysql%'))->exists();
        $hasKibanaIncident = Incident::where('source', 'like', '%Kibana%')->exists();
        $hasZabbixIncident = Incident::where('source', 'like', '%Zabbix%')->exists();

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
            'incidents'       => $incidents,
            'totalIncidents'  => $total,
            'criticalCount'   => $critical,
            'warningCount'    => $warning,
            'infoCount'       => $info,
            'services'        => $services,
            'kibanaErrorRate' => Incident::where('source', 'like', '%Kibana%')->count(),
            'systemHealth'    => $critical > 0 ? 'CRITICAL OUTAGE' : ($warning > 0 ? 'SYSTEM DEGRADED' : 'ALL SYSTEMS NOMINAL'),
        ];
    }
};
?>

<div x-data="{ 
        darkMode: localStorage.getItem('vigilcore_theme') ? localStorage.getItem('vigilcore_theme') === 'dark' : true, 
        pollingActive: true 
     }" 
     x-init="$watch('darkMode', val => localStorage.setItem('vigilcore_theme', val ? 'dark' : 'light'))"
     :class="darkMode ? 'bg-[#0f131d] text-[#dfe2f1]' : 'bg-[#f8fafc] text-slate-900'" 
     class="min-h-screen font-sans selection:bg-[#7c3aed] selection:text-white transition-colors duration-300"
     x-bind:wire:poll.5s="pollingActive ? true : false">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap');
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .font-sans { font-family: 'Inter', sans-serif; }

        .btn-liquid-violet {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(124, 58, 237, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-liquid-violet::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #7c3aed 0%, #ee9800 100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1;
        }
        .btn-liquid-violet:hover::before {
            transform: translateY(-100%);
        }
        .btn-liquid-violet span {
            position: relative;
            z-index: 2;
        }
    </style>

    <!-- Top Sticky Header -->
    <header class="sticky top-0 z-40 border-b backdrop-blur-xl transition-colors duration-300"
            :class="darkMode ? 'bg-[#0f131d]/85 border-[#262a35]' : 'bg-white/95 border-slate-200 shadow-sm'">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-[#7c3aed] to-[#ee9800] flex items-center justify-center text-white font-bold text-sm shadow-md">
                        VC
                    </div>
                    <div>
                        <h1 class="font-extrabold text-base tracking-tight flex items-center gap-2"
                            :class="darkMode ? 'text-white' : 'text-slate-900'">
                            VigilCore <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded border"
                                :class="darkMode ? 'bg-violet-950/40 border-violet-800/60 text-[#d2bbff]' : 'bg-violet-100 border-violet-300 text-violet-800'">OPS-01</span>
                        </h1>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-semibold border"
                     :class="criticalCount > 0 
                        ? (darkMode ? 'bg-red-950/40 border-red-900/60 text-red-400' : 'bg-red-100 border-red-300 text-red-700')
                        : (darkMode ? 'bg-emerald-950/40 border-emerald-900/60 text-[#4edea3]' : 'bg-emerald-100 border-emerald-300 text-emerald-800')">
                    <span class="w-2 h-2 rounded-full"
                          :class="criticalCount > 0 ? 'bg-red-500 animate-ping' : 'bg-emerald-500 animate-pulse'"></span>
                    <span>{{ $systemHealth }}</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                
                <!-- Polling Switch -->
                <div class="hidden md:flex items-center gap-2.5 px-3 py-1.5 rounded-lg border text-xs font-mono font-semibold"
                     :class="darkMode ? 'bg-[#171b26] border-[#262a35] text-slate-300' : 'bg-slate-100 border-slate-300 text-slate-800'">
                    <span>Live 5s</span>
                    <button type="button" 
                            @click="pollingActive = !pollingActive" 
                            :class="pollingActive ? 'bg-[#7c3aed] shadow-[0_0_12px_rgba(124,58,237,0.5)]' : 'bg-slate-400'" 
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none">
                        <span :class="pollingActive ? 'translate-x-4' : 'translate-x-0'" 
                              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"></span>
                    </button>
                </div>

                <!-- Dark/Light Mode Switch -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-xs font-mono font-semibold"
                     :class="darkMode ? 'bg-[#171b26] border-[#262a35] text-slate-200' : 'bg-slate-100 border-slate-300 text-slate-900'">
                    <span x-text="darkMode ? 'Dark' : 'Light'"></span>
                    <button type="button" 
                            @click="darkMode = !darkMode" 
                            :class="darkMode ? 'bg-[#7c3aed] shadow-[0_0_12px_rgba(124,58,237,0.5)]' : 'bg-amber-500 shadow-[0_0_12px_rgba(238,152,0,0.4)]'" 
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none">
                        <span :class="darkMode ? 'translate-x-4' : 'translate-x-0'" 
                              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"></span>
                    </button>
                </div>

                <!-- Simulation Trigger -->
                <div class="relative inline-block" x-data="{ openMenu: false }">
                    <button @click="openMenu = !openMenu" 
                            class="btn-liquid-violet rounded-lg px-4 py-2 text-xs font-mono font-bold"
                            :class="darkMode ? 'bg-[#171b26] text-[#d2bbff]' : 'bg-violet-50 border-violet-300 text-violet-800 hover:text-white'">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Simuler Webhook
                        </span>
                    </button>

                    <div x-show="openMenu" @click.away="openMenu = false" 
                         class="absolute right-0 mt-2 w-60 rounded-xl border p-2 shadow-2xl z-50 transition-all font-mono text-xs font-semibold"
                         :class="darkMode ? 'bg-[#1c1f2a] border-[#262a35] text-slate-200' : 'bg-white border-slate-300 text-slate-800 shadow-xl'">
                        <button wire:click="triggerSimulation('kibana')" @click="openMenu = false" class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-500/10 hover:text-red-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Crash Kibana (500 S3P)
                        </button>
                        <button wire:click="triggerSimulation('zabbix')" @click="openMenu = false" class="w-full text-left px-3 py-2 rounded-lg hover:bg-amber-500/10 hover:text-amber-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Alerte Zabbix (RAM > 88%)
                        </button>
                        <button wire:click="triggerSimulation('n8n')" @click="openMenu = false" class="w-full text-left px-3 py-2 rounded-lg hover:bg-violet-500/10 hover:text-violet-600 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                            Webhook n8n Dispatch
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- KPI Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            
            <!-- Total Tracked -->
            <div class="rounded-xl p-5 border transition-all duration-300"
                 :class="darkMode ? 'bg-[#171b26] border-[#262a35]' : 'bg-white border-slate-200 shadow-sm'">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider font-mono"
                           :class="darkMode ? 'text-slate-400' : 'text-slate-600'">Total Tracked</p>
                        <h3 class="text-3xl font-black font-mono mt-1" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $totalIncidents }}</h3>
                    </div>
                    <div class="p-2.5 rounded-lg border"
                         :class="darkMode ? 'bg-violet-950/40 border-violet-900/50 text-[#d2bbff]' : 'bg-violet-100 border-violet-300 text-violet-700'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-[11px] font-mono border-t pt-3"
                     :class="darkMode ? 'border-[#262a35] text-slate-400' : 'border-slate-200 text-slate-700'">
                    <span>Événements analysés</span>
                    <span class="font-bold" :class="darkMode ? 'text-[#4edea3]' : 'text-emerald-600'">100% Ingest</span>
                </div>
            </div>

            <!-- Critical Breaches -->
            <div class="rounded-xl p-5 border transition-all duration-300"
                 :class="darkMode ? 'bg-[#171b26] border-red-900/40' : 'bg-white border-red-200 shadow-sm'">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider font-mono flex items-center gap-1.5"
                           :class="darkMode ? 'text-red-400' : 'text-red-600'">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                            Breaches Critiques
                        </p>
                        <h3 class="text-3xl font-black font-mono mt-1 text-red-600">{{ $criticalCount }}</h3>
                    </div>
                    <div class="p-2.5 rounded-lg border"
                         :class="darkMode ? 'bg-red-950/40 border-red-900/50 text-red-400' : 'bg-red-100 border-red-300 text-red-600'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-[11px] font-mono border-t pt-3"
                     :class="darkMode ? 'border-[#262a35] text-slate-400' : 'border-slate-200 text-slate-700'">
                    <span>Seuil critique (>0)</span>
                    <span class="font-bold" :class="criticalCount > 0 ? (darkMode ? 'text-red-400' : 'text-red-600') : (darkMode ? 'text-[#4edea3]' : 'text-emerald-600')">
                        {{ $criticalCount > 0 ? 'Action Requise' : 'Nominal' }}
                    </span>
                </div>
            </div>

            <!-- Kibana Error Rate -->
            <div class="rounded-xl p-5 border transition-all duration-300"
                 :class="darkMode ? 'bg-[#171b26] border-[#262a35]' : 'bg-white border-slate-200 shadow-sm'">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider font-mono"
                           :class="darkMode ? 'text-[#ffb95f]' : 'text-amber-700'">Kibana Error Rate</p>
                        <h3 class="text-3xl font-black font-mono mt-1" :class="darkMode ? 'text-white' : 'text-slate-900'">
                            {{ $kibanaErrorRate }} <span class="text-xs font-normal" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">/min</span>
                        </h3>
                    </div>
                    <div class="p-2.5 rounded-lg border"
                         :class="darkMode ? 'bg-amber-950/40 border-amber-900/50 text-[#ffb95f]' : 'bg-amber-100 border-amber-300 text-amber-700'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-[11px] font-mono border-t pt-3"
                     :class="darkMode ? 'border-[#262a35] text-slate-400' : 'border-slate-200 text-slate-700'">
                    <span>Pattern: `*500*`</span>
                    <span class="font-bold" :class="darkMode ? 'text-violet-400' : 'text-violet-700'">srv901529</span>
                </div>
            </div>

            <!-- n8n Dispatcher -->
            <div class="rounded-xl p-5 border transition-all duration-300"
                 :class="darkMode ? 'bg-[#171b26] border-[#262a35]' : 'bg-white border-slate-200 shadow-sm'">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider font-mono"
                           :class="darkMode ? 'text-[#4edea3]' : 'text-emerald-700'">n8n Dispatcher</p>
                        <h3 class="text-3xl font-black font-mono mt-1" :class="darkMode ? 'text-[#4edea3]' : 'text-emerald-600'">Active</h3>
                    </div>
                    <div class="p-2.5 rounded-lg border"
                         :class="darkMode ? 'bg-emerald-950/40 border-emerald-900/50 text-[#4edea3]' : 'bg-emerald-100 border-emerald-300 text-emerald-700'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-[11px] font-mono border-t pt-3"
                     :class="darkMode ? 'border-[#262a35] text-slate-400' : 'border-slate-200 text-slate-700'">
                    <span>Sync Statuspage</span>
                    <span class="font-bold" :class="darkMode ? 'text-emerald-400' : 'text-emerald-700'">24/7 Live</span>
                </div>
            </div>
        </div>

        <!-- Service Matrix Grid & Severity Donut -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Real-Time Service Health Matrix -->
            <div class="lg:col-span-2 rounded-xl p-6 border transition-all"
                 :class="darkMode ? 'bg-[#171b26] border-[#262a35]' : 'bg-white border-slate-200 shadow-sm'">
                <div class="flex items-center justify-between mb-4 pb-3 border-b"
                     :class="darkMode ? 'border-[#262a35]' : 'border-slate-200'">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider font-mono" :class="darkMode ? 'text-white' : 'text-slate-900'">Service Health Matrix</h2>
                        <p class="text-xs font-medium mt-0.5" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">Surveillance granulaire des composants critiques</p>
                    </div>
                    <span class="text-xs font-mono font-bold" :class="darkMode ? 'text-violet-400' : 'text-violet-700'">Node: srv901529</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($services as $svc)
                        <div class="p-3.5 rounded-lg border flex flex-col justify-between transition-all"
                             :class="darkMode ? 'bg-[#1c1f2a] border-[#262a35]' : 'bg-slate-50 border-slate-200'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold" :class="darkMode ? 'text-slate-200' : 'text-slate-900'">{{ $svc['name'] }}</span>
                                <div class="relative flex h-2.5 w-2.5">
                                    @if($svc['status'] === 'degraded')
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                    @elseif($svc['status'] === 'warning')
                                        <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                    @else
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs font-mono"
                                 :class="darkMode ? 'text-slate-400' : 'text-slate-700'">
                                <span>Latence: <strong :class="'{{ $svc['status'] }}' === 'degraded' ? 'text-red-600' : (darkMode ? 'text-slate-200' : 'text-slate-900')">{{ $svc['latency'] }}</strong></span>
                                <span class="font-bold" :class="darkMode ? 'text-[#7c3aed]' : 'text-violet-700'">SLA {{ $svc['sla'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Severity Distribution Card -->
            <div class="rounded-xl p-6 border flex flex-col justify-between"
                 :class="darkMode ? 'bg-[#171b26] border-[#262a35]' : 'bg-white border-slate-200 shadow-sm'">
                <div class="flex items-center justify-between mb-4 pb-3 border-b"
                     :class="darkMode ? 'border-[#262a35]' : 'border-slate-200'">
                    <h2 class="text-sm font-bold uppercase tracking-wider font-mono" :class="darkMode ? 'text-white' : 'text-slate-900'">Severity Breakdown</h2>
                    <span class="text-xs font-mono font-semibold" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">Ratio Actif</span>
                </div>

                <div class="space-y-4 font-mono text-xs">
                    <!-- Critical Bar -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-bold text-red-600">Critique</span>
                            <span class="font-bold" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $criticalCount }}</span>
                        </div>
                        <div class="w-full rounded-full h-2.5 overflow-hidden" :class="darkMode ? 'bg-slate-800' : 'bg-slate-200'">
                            <div class="bg-red-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $totalIncidents > 0 ? ($criticalCount / $totalIncidents) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Warning Bar -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-bold" :class="darkMode ? 'text-amber-400' : 'text-amber-700'">Warning</span>
                            <span class="font-bold" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $warningCount }}</span>
                        </div>
                        <div class="w-full rounded-full h-2.5 overflow-hidden" :class="darkMode ? 'bg-slate-800' : 'bg-slate-200'">
                            <div class="bg-[#ee9800] h-2.5 rounded-full transition-all duration-500" style="width: {{ $totalIncidents > 0 ? ($warningCount / $totalIncidents) * 100 : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Info Bar -->
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="font-bold" :class="darkMode ? 'text-[#d2bbff]' : 'text-violet-700'">Info</span>
                            <span class="font-bold" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $infoCount }}</span>
                        </div>
                        <div class="w-full rounded-full h-2.5 overflow-hidden" :class="darkMode ? 'bg-slate-800' : 'bg-slate-200'">
                            <div class="bg-[#7c3aed] h-2.5 rounded-full transition-all duration-500" style="width: {{ $totalIncidents > 0 ? ($infoCount / $totalIncidents) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-lg border text-center font-mono text-[11px] font-semibold"
                     :class="darkMode ? 'bg-[#1c1f2a] border-[#262a35] text-slate-300' : 'bg-slate-100 border-slate-200 text-slate-800'">
                    Synchronisation active avec <strong class="text-violet-600">Atlassian Statuspage</strong>
                </div>
            </div>
        </div>

        <!-- Live Incident Stream Section -->
        <div class="rounded-xl border p-6 shadow-2xl backdrop-blur-md transition-colors"
             :class="darkMode ? 'bg-[#171b26]/90 border-[#262a35]' : 'bg-white border-slate-200 shadow-sm'">
            
            <!-- Stream Header & Filters -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 pb-4 border-b gap-4"
                 :class="darkMode ? 'border-[#262a35]' : 'border-slate-200'">
                <div>
                    <h2 class="text-lg font-bold tracking-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">Flux des Incidents & Triage Direct</h2>
                    <p class="text-xs font-mono mt-0.5" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">Payloads JSON routés en temps réel depuis Kibana, Zabbix et n8n</p>
                </div>
                
                <!-- Filters Bar -->
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="$set('filter', 'all')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-mono font-bold transition-all border"
                        :class="darkMode 
                            ? '{{ $filter === 'all' ? 'bg-[#7c3aed]/20 border-[#7c3aed] text-[#d2bbff]' : 'border-slate-700/50 text-slate-300 hover:text-white' }}'
                            : '{{ $filter === 'all' ? 'bg-violet-600 text-white shadow-sm border-violet-600' : 'bg-slate-100 border-slate-300 text-slate-800 hover:bg-slate-200' }}'">
                        Tous ({{ $totalIncidents }})
                    </button>
                    <button wire:click="$set('filter', 'critical')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-mono font-bold transition-all border"
                        :class="darkMode 
                            ? '{{ $filter === 'critical' ? 'bg-red-500/20 border-red-500 text-red-400' : 'border-slate-700/50 text-slate-300 hover:text-white' }}'
                            : '{{ $filter === 'critical' ? 'bg-red-600 text-white shadow-sm border-red-600' : 'bg-slate-100 border-slate-300 text-slate-800 hover:bg-slate-200' }}'">
                        Critiques ({{ $criticalCount }})
                    </button>
                    <button wire:click="$set('filter', 'warning')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-mono font-bold transition-all border"
                        :class="darkMode 
                            ? '{{ $filter === 'warning' ? 'bg-amber-500/20 border-amber-500 text-amber-400' : 'border-slate-700/50 text-slate-300 hover:text-white' }}'
                            : '{{ $filter === 'warning' ? 'bg-amber-600 text-white shadow-sm border-amber-600' : 'bg-slate-100 border-slate-300 text-slate-800 hover:bg-slate-200' }}'">
                        Warnings ({{ $warningCount }})
                    </button>
                    <button wire:click="$set('filter', 'info')" 
                        class="px-3 py-1.5 rounded-lg text-xs font-mono font-bold transition-all border"
                        :class="darkMode 
                            ? '{{ $filter === 'info' ? 'bg-violet-500/20 border-violet-500 text-[#d2bbff]' : 'border-slate-700/50 text-slate-300 hover:text-white' }}'
                            : '{{ $filter === 'info' ? 'bg-violet-700 text-white shadow-sm border-violet-700' : 'bg-slate-100 border-slate-300 text-slate-800 hover:bg-slate-200' }}'">
                        Infos ({{ $infoCount }})
                    </button>
                </div>
            </div>

            <!-- Incident Cards Feed -->
            @if($incidents->isEmpty())
                <div class="py-16 text-center">
                    <div class="inline-flex p-4 rounded-full border mb-4"
                         :class="darkMode ? 'bg-[#1c1f2a] border-[#262a35]' : 'bg-emerald-50 border-emerald-200'">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="font-bold text-sm" :class="darkMode ? 'text-slate-200' : 'text-slate-900'">Aucun incident détecté</p>
                    <p class="text-xs font-mono mt-1 font-semibold" :class="darkMode ? 'text-slate-400' : 'text-slate-600'">Toutes les sondes Zabbix et Kibana confirment le statut nominal.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($incidents as $incident)
                        @php
                            $severity = strtolower($incident->severity ?? 'info');
                            $badgeColor = match($severity) {
                                'critical' => 'bg-red-500/10 border-red-500/40 text-red-600 font-bold',
                                'warning' => 'bg-amber-500/10 border-amber-500/40 text-amber-700 font-bold',
                                default => 'bg-violet-500/10 border-violet-500/40 text-violet-700 font-bold'
                            };
                        @endphp
                        <div class="group border rounded-xl p-4 transition-all duration-200 hover:translate-x-1"
                             :class="darkMode ? 'bg-[#1c1f2a]/70 border-[#262a35] hover:border-slate-600' : 'bg-slate-50 border-slate-200 hover:border-slate-300'">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                
                                <div class="flex items-start gap-3.5">
                                    <div class="mt-0.5 px-2.5 py-1 rounded-md text-[10px] font-mono uppercase tracking-wider border {{ $badgeColor }}">
                                        {{ $incident->severity ?? 'INFO' }}
                                    </div>
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="text-sm font-bold transition-colors"
                                                :class="darkMode ? 'text-white group-hover:text-violet-400' : 'text-slate-950 group-hover:text-violet-700'">
                                                {{ $incident->title }}
                                            </h3>
                                            <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded border"
                                                  :class="darkMode ? 'bg-[#171b26] border-[#262a35] text-slate-300' : 'bg-white border-slate-300 text-slate-800'">
                                                {{ $incident->source }}
                                            </span>
                                        </div>
                                        <p class="text-xs font-medium mt-1 leading-relaxed"
                                           :class="darkMode ? 'text-slate-300' : 'text-slate-800'">
                                            {{ $incident->description }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 self-end md:self-center font-mono">
                                    <span class="text-[11px] font-semibold" :class="darkMode ? 'text-slate-400' : 'text-slate-600'"
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
                                          }" x-init="update(); setInterval(() => update(), 1000)" x-text="timeAgo">
                                        {{ $incident->created_at->diffForHumans() }}
                                    </span>
                                    
                                    <button wire:click="inspectPayload({{ $incident->id }})" 
                                        class="px-2.5 py-1.5 rounded-lg text-xs font-bold border transition-all"
                                        :class="darkMode ? 'bg-[#171b26] border-[#262a35] text-slate-200 hover:border-violet-500' : 'bg-white border-slate-300 text-slate-900 hover:border-violet-600 shadow-sm'">
                                        JSON
                                    </button>

                                    <button wire:click="resolveIncident({{ $incident->id }})" 
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold border transition-all shadow-sm"
                                        :class="darkMode ? 'text-[#4edea3] bg-emerald-500/10 border-emerald-500/30 hover:bg-emerald-500 hover:text-slate-950' : 'text-white bg-emerald-600 border-emerald-700 hover:bg-emerald-700'">
                                        Résoudre
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <!-- Modal Visualiseur JSON -->
    @if($selectedIncidentId && $activePayload)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
             wire:click.self="closePayloadModal">
            <div class="w-full max-w-2xl rounded-2xl border p-6 shadow-2xl transition-all font-mono"
                 :class="darkMode ? 'bg-[#171b26] border-[#262a35] text-slate-200' : 'bg-white border-slate-300 text-slate-900'">
                <div class="flex justify-between items-center pb-3 border-b mb-4"
                     :class="darkMode ? 'border-[#262a35]' : 'border-slate-200'">
                    <h3 class="text-sm font-bold flex items-center gap-2" :class="darkMode ? 'text-[#d2bbff]' : 'text-violet-800'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                        Telemetry Raw Payload (ID #{{ $selectedIncidentId }})
                    </h3>
                    <button wire:click="closePayloadModal" class="text-slate-400 hover:text-slate-700 text-lg font-bold">&times;</button>
                </div>
                <pre class="p-4 rounded-xl text-xs overflow-x-auto border leading-relaxed bg-[#0f131d] border-[#262a35] text-[#4edea3]">{{ json_encode($activePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                <div class="mt-4 flex justify-end">
                    <button wire:click="closePayloadModal" class="px-4 py-1.5 rounded-lg text-xs font-bold bg-[#7c3aed] text-white hover:bg-violet-600 transition-all">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>