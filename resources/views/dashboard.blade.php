<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Executive Dashboard — Disavo Operating System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        dark: {
                            base: '#0c0e14',
                            surface: '#131620',
                            card: '#161a26',
                            border: '#1e2333',
                            hover: '#1d2334',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0f19; color: #f1f5f9; }
        .modal-open { overflow: hidden; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0b0f19; }
        ::-webkit-scrollbar-thumb { background: #27354f; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #3b82f6; }
    </style>
</head>
<body class="bg-[#0b0f19] text-slate-100 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="border-b border-dark-border bg-[#0e1422]/95 backdrop-blur sticky top-0 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center font-extrabold text-white text-base font-mono shadow-md shadow-blue-500/20 hover:bg-blue-500 transition">
                    D
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-extrabold text-sm tracking-tight text-white">DISAVO</span>
                        <span class="text-blue-500 font-mono text-xs font-bold">OS</span>
                        <span class="text-slate-600 font-mono text-xs">/</span>
                        <span class="text-xs font-semibold text-slate-300">{{ $tenant->name ?? 'Disavo Holding GmbH' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- User Profile & Settings Trigger -->
                <button 
                    onclick="openProfileModal()"
                    title="Benutzerprofil &amp; Passwort bearbeiten"
                    class="flex items-center space-x-2.5 px-3 py-1.5 rounded-lg bg-dark-surface border border-dark-border hover:border-blue-500/60 hover:bg-dark-hover transition text-left cursor-pointer group shadow-sm"
                >
                    <div class="w-6 h-6 rounded-full bg-blue-500/20 group-hover:bg-blue-500/30 text-blue-400 font-bold text-xs flex items-center justify-center font-mono transition">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="text-left hidden sm:block">
                        <div class="text-xs font-bold text-white leading-tight flex items-center space-x-1">
                            <span>{{ auth()->user()->name }}</span>
                            <span class="text-[10px] text-slate-400 group-hover:text-blue-400 transition">⚙️</span>
                        </div>
                        <div class="text-[10px] text-blue-400 font-mono leading-tight">{{ auth()->user()->email }}</div>
                    </div>
                </button>

                <!-- Quick Action Buttons in Header -->
                <button 
                    onclick="openUserModal()"
                    class="px-3 py-1.5 rounded-lg bg-[#1a2333] hover:bg-[#223049] border border-blue-500/40 text-blue-300 font-semibold text-xs transition shadow-sm flex items-center space-x-1.5"
                >
                    <span>👥 Add User</span>
                </button>

                <button 
                    onclick="openCaptureModal()"
                    class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs transition shadow-sm flex items-center space-x-1.5"
                >
                    <span>⚡ Quick Capture</span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-dark-border hover:bg-red-500/10 hover:border-red-500/30 text-xs font-medium text-slate-300 hover:text-red-400 transition">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full space-y-8">

        @php
            $latestCompletedAmar = $auditRuns->where('status', 'completed')->first(fn ($r) => $r->isAmar());
            $activeFeatureBan = $latestCompletedAmar && $latestCompletedAmar->hasFeatureBan();
        @endphp

        @if ($activeFeatureBan)
            <div class="p-4 rounded-xl bg-rose-950/90 border-2 border-rose-500 text-rose-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xl animate-pulse">
                <div class="flex items-center space-x-3">
                    <span class="w-9 h-9 rounded-xl bg-rose-500/30 text-rose-300 font-extrabold flex items-center justify-center text-lg">🚨</span>
                    <div>
                        <h4 class="text-sm font-extrabold uppercase tracking-wider text-rose-200">NEW FEATURE BAN AKTIV (AMAR Kybernetischer Regelkreis)</h4>
                        <p class="text-xs text-rose-100 mt-0.5">
                            Im letzten <strong>ALLOCORE MASTER AUDIT (AMAR)</strong> wurde festgestellt, dass Allocore noch nicht nachweisbar wertvoller für den Unternehmer geworden ist.
                            <strong>Neue Feature-Entwicklung ist gesperrt</strong>, bis die dokumentierten Mängel aus dem Insight-Protokoll behoben sind!
                        </p>
                    </div>
                </div>
                <button onclick="openArfModal(); switchArfTab('history');" class="px-3.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow transition whitespace-nowrap">
                    Mängelprotokoll ansehen &rarr;
                </button>
            </div>
        @endif

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-950/80 border-2 border-emerald-500 text-emerald-100 flex items-center justify-between shadow-xl">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center text-sm">✓</span>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200 font-bold text-sm px-2">✕</button>
            </div>
        @endif

        @if (session('info'))
            <div class="p-4 rounded-xl bg-blue-950/80 border-2 border-blue-500 text-blue-100 flex items-center justify-between shadow-xl">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-full bg-blue-500/20 text-blue-400 font-bold flex items-center justify-center text-sm">ℹ</span>
                    <span class="text-sm font-semibold">{{ session('info') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-blue-400 hover:text-blue-200 font-bold text-sm px-2">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-950/80 border-2 border-rose-500 text-rose-100 flex items-center justify-between shadow-xl">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-full bg-rose-500/20 text-rose-400 font-bold flex items-center justify-center text-sm">⚠️</span>
                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200 font-bold text-sm px-2">✕</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-rose-950/80 border-2 border-rose-500 text-rose-100 flex items-start justify-between shadow-xl">
                <div class="flex items-start space-x-3">
                    <span class="w-7 h-7 rounded-full bg-rose-500/20 text-rose-400 font-bold flex items-center justify-center text-sm mt-0.5">⚠️</span>
                    <div>
                        <span class="text-sm font-bold block">Fehler bei der Anfrage:</span>
                        <ul class="text-xs list-disc list-inside mt-1 space-y-0.5 text-rose-200">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-200 font-bold text-sm px-2">✕</button>
            </div>
        @endif

        <!-- Executive Hero & Command Center Banner -->
        <div class="p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-dark-surface to-[#162032] border border-dark-border shadow-xl">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/15 border border-blue-500/30 text-blue-400 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 -ml-3"></span>
                        <span>ENTERPRISE OPERATING SYSTEM ACTIVE</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                        Disavo Holding — Strategic Operating System
                    </h1>
                    <p class="text-sm sm:text-base text-slate-300 mt-2 max-w-2xl leading-relaxed">
                        Continuous corporate governance platform combining <strong class="text-white">Knowledge Evolution (ALF)</strong>, <strong class="text-white">Modular Reifegrad Audits (AMF)</strong>, and <strong class="text-white">Review Cycles (ARF)</strong>.
                    </p>
                </div>

                <!-- Executive Framework & Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                    <!-- Core 3 Framework Hubs -->
                    <button 
                        onclick="openAmfModal()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs shadow-md shadow-blue-600/30 transition flex items-center space-x-1.5 border border-blue-400/30"
                    >
                        <span>🛠️</span>
                        <span>AMF Tool-Studio</span>
                    </button>

                    <button 
                        onclick="openArfModal()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-md shadow-emerald-600/30 transition flex items-center space-x-1.5 border border-emerald-400/30"
                    >
                        <span>🎯</span>
                        <span>ARF Audit-Center</span>
                    </button>

                    <button 
                        onclick="openAlfModal()"
                        class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-500 hover:to-pink-500 text-white font-extrabold text-xs shadow-md shadow-purple-600/30 transition flex items-center space-x-1.5 border border-purple-400/30"
                    >
                        <span>🧠</span>
                        <span>ALF Prinzipienschmiede</span>
                    </button>

                    <!-- Fast Actions -->
                    <button 
                        onclick="openCaptureModal()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center space-x-1.5"
                    >
                        <span>⚡</span>
                        <span>Quick Capture</span>
                    </button>

                    <button 
                        onclick="openAuditModal()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center space-x-1.5"
                    >
                        <span>📋</span>
                        <span>Audit starten</span>
                    </button>

                    <button 
                        onclick="openReviewCreateModal()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center space-x-1.5"
                    >
                        <span>🔄</span>
                        <span>Review ansetzen</span>
                    </button>

                    <button 
                        onclick="openModuleCreateModal()"
                        class="px-3.5 py-2.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 font-bold text-xs transition flex items-center space-x-1.5"
                    >
                        <span>🧩</span>
                        <span>Modul entwickeln</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- How To Use DOS: 3-Step Guidance Workflow -->
        <div class="p-6 rounded-2xl bg-dark-surface border border-dark-border shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center space-x-2">
                        <span class="text-blue-400">ℹ️</span>
                        <span>How DOS Works — Operational Executive Workflow</span>
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5">Understand how the 3 core engines interact seamlessly to govern your holding portfolio.</p>
                </div>
                <span class="hidden sm:inline-block px-2.5 py-1 rounded bg-slate-800 text-slate-300 font-mono text-[11px] font-semibold">
                    Loop Closes Automatically
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <!-- Step 1 -->
                <div class="p-4 rounded-xl bg-[#162032] border border-blue-500/20 hover:border-blue-500/40 transition">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 font-bold font-mono flex items-center justify-center text-xs">1</span>
                        <span class="font-bold text-white text-sm">AMF 1.1 — Die Modulfabrik</span>
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Definiert, <strong class="text-white">wie Module entstehen</strong>. Jedes Modul folgt einer 9-teiligen Blaupause über die <strong class="text-white">5 Allocore-Ebenen</strong> (Umsatz, Gewinn, Ordnung, Einfluss, Vermächtnis).
                    </p>
                    <div class="mt-3 pt-3 border-t border-dark-border flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Aktion:</span>
                        <button onclick="openAmfModal()" class="text-blue-400 hover:text-blue-300 font-semibold underline">Blaupause &amp; 9 Bausteine &rarr;</button>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="p-4 rounded-xl bg-[#162032] border border-purple-500/20 hover:border-purple-500/40 transition">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-purple-500/20 text-purple-400 font-bold font-mono flex items-center justify-center text-xs">2</span>
                        <span class="font-bold text-white text-sm">ALF — Knowledge & Principles</span>
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Capture daily market <strong class="text-white">Observations</strong>. Knowledge Stewards validate them into <strong class="text-white">Principles</strong> that directly govern business modules.
                    </p>
                    <div class="mt-3 pt-3 border-t border-dark-border flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Action:</span>
                        <button onclick="openCaptureModal()" class="text-purple-400 hover:text-purple-300 font-semibold underline">Log Observation &rarr;</button>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="p-4 rounded-xl bg-[#162032] border border-emerald-500/20 hover:border-emerald-500/40 transition">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 font-bold font-mono flex items-center justify-center text-xs">3</span>
                        <span class="font-bold text-white text-sm">ARF — Review Loop Closure</span>
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Hold quarterly strategic reviews. When marked as <strong class="text-white">Closed</strong>, DOS automatically spawns a <strong class="text-white">Draft Learning</strong> in ALF, closing the feedback loop.
                    </p>
                    <div class="mt-3 pt-3 border-t border-dark-border flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Action:</span>
                        <button onclick="openArfModal()" class="text-emerald-400 hover:text-emerald-300 font-semibold underline">Inspect Reviews &rarr;</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Cards Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">AMF Modules</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                </div>
                <div class="text-3xl font-extrabold font-mono text-white mt-2">{{ count($modules) }}</div>
                <div class="text-xs text-slate-300 mt-1">Active Core Pillars</div>
            </div>

            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Governing Laws</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                </div>
                <div class="text-3xl font-extrabold font-mono text-purple-400 mt-2">{{ $principlesCount }}</div>
                <div class="text-xs text-slate-300 mt-1">ALF Active Principles</div>
            </div>

            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Graph Edges</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>
                <div class="text-3xl font-extrabold font-mono text-emerald-400 mt-2">{{ $edgesCount }}</div>
                <div class="text-xs text-slate-300 mt-1">Knowledge Connections</div>
            </div>

            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Strategic Reviews</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                </div>
                <div class="text-3xl font-extrabold font-mono text-amber-400 mt-2">{{ $reviewsCount }}</div>
                <div class="text-xs text-slate-300 mt-1">ARF Review Cycles</div>
            </div>
        </div>

        <!-- The 6 Canonical Development Modules Grid -->
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-white tracking-tight flex items-center space-x-2">
                        <span>Six Canonical Development Modules</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-blue-500/20 text-blue-400 border border-blue-500/30">AMF Tree</span>
                    </h2>
                    <p class="text-sm text-slate-300 mt-0.5">Click any card to inspect goals, principles, and tools — or click <strong class="text-white">"Run Audit"</strong> to score a module directly.</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="openAmfModal()" class="px-3 py-1.5 rounded-lg bg-dark-surface hover:bg-dark-hover border border-dark-border text-xs font-semibold text-slate-300 hover:text-white transition">
                        View AMF Architecture &rarr;
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($modules as $module)
                    @php
                        $latestAudit = $module->auditRuns->sortByDesc('completed_at')->first();
                        $latestKpi = $module->kpis->first();
                        $latestReading = $latestKpi?->latestReading();
                        $firstPrinciple = $module->governing_principles?->first();

                        $moduleSubtitles = [
                            'unternehmensentwicklung' => 'Company Development (Enterprise & Scaling)',
                            'markenaufbau'            => 'Brand Building (Positioning & Authority)',
                            'nachfolge'               => 'Succession (Generational Handover)',
                            'unternehmerentwicklung'  => 'Entrepreneur Development (Founder & Leadership)',
                            'beteiligungsmanagement'  => 'Investment Management (Portfolio Steering)',
                            'kapitalallokation'       => 'Capital Allocation (Reinvestment & Liquidity)',
                        ];
                        $subtitle = $moduleSubtitles[$module->slug] ?? null;
                    @endphp
                    <div class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-blue-500/60 hover:bg-[#162032] transition duration-200 shadow-md flex flex-col justify-between group">
                        <div>
                            <!-- Header: Module sequence & badge -->
                            <div class="flex items-center justify-between pb-3 border-b border-dark-border/60">
                                <span class="text-xs font-mono font-bold text-blue-400 uppercase tracking-wide">
                                    Module 0{{ $loop->iteration }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    Active Pillar
                                </span>
                            </div>

                            <!-- Title & Description -->
                            <h3 class="text-lg font-bold text-white mt-3 group-hover:text-blue-400 transition leading-snug">
                                {{ $module->name }}
                            </h3>
                            @if ($subtitle)
                                <span class="text-xs font-semibold text-blue-400 block mt-0.5">
                                    {{ $subtitle }}
                                </span>
                            @endif
                            <p class="text-xs text-slate-300 mt-2 leading-relaxed line-clamp-2">
                                {{ $module->description ?? 'Core operational pillar of Disavo Holding corporate development.' }}
                            </p>

                            <!-- Live Metric Badges -->
                            <div class="mt-4 pt-3 border-t border-dark-border/40 space-y-2">
                                <!-- Reifegrad Score -->
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Reifegrad (Maturity):</span>
                                    @if ($latestAudit && $latestAudit->overall_score !== null)
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 font-mono font-bold border border-emerald-500/30">
                                            {{ $latestAudit->overall_score }}%
                                        </span>
                                    @else
                                        <span class="text-slate-500 font-mono">Pending</span>
                                    @endif
                                </div>

                                <!-- Live KPI -->
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Primary KPI:</span>
                                    @if ($latestKpi)
                                        <span class="font-mono text-white font-bold truncate max-w-[170px]" title="{{ $latestKpi->name }}">
                                            {{ $latestReading ? $latestReading->value . ' ' . $latestKpi->unit : 'Target: ' . $latestKpi->target_value . ' ' . $latestKpi->unit }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 font-mono">—</span>
                                    @endif
                                </div>

                                <!-- Governing Principle -->
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Governed By:</span>
                                    @if ($firstPrinciple)
                                        <span class="text-purple-300 font-medium truncate max-w-[170px]" title="{{ $firstPrinciple->title }}">
                                            ⚖️ {{ $firstPrinciple->title }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 font-mono">None</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="mt-5 pt-4 border-t border-dark-border flex items-center justify-between gap-2">
                            <button 
                                onclick="openModuleModal({{ $module->id }})"
                                class="flex-1 py-2 px-3 rounded-xl bg-dark-hover hover:bg-slate-700 text-white font-semibold text-xs transition border border-dark-border text-center"
                            >
                                Inspect Pillar &rarr;
                            </button>

                            <button 
                                onclick="openAuditModalForModule({{ $module->id }})"
                                class="py-2 px-3 rounded-xl bg-blue-600/90 hover:bg-blue-600 text-white font-bold text-xs transition shadow-sm text-center"
                                title="Run an audit questionnaire for this module"
                            >
                                Run Audit
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3 Pillars Overview (Clickable Cards) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">Zentrale System-Engines (ALF / AMF / ARF)</h2>
                    <p class="text-xs text-slate-300">Klicken Sie auf eine Engine-Karte, um Live-Daten, kognitive Pfade, Audit-Vorlagen und die Regelkreis-Schließung (Loop Closure) einzusehen.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- ALF Engine Card -->
                <div 
                    onclick="openAlfModal()"
                    class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between shadow-md"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-blue-400 uppercase">ALF-ENGINE</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-blue-400 transition">Kognitive Nachvollziehbarkeit</h3>
                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                            Entscheidungen werden rückwirkend auf empirische Beobachtungen zurückgeführt. Prinzipien aktualisieren sich zerstörungsfrei über versionierte Supersedes-Relationen.
                        </p>
                        <div class="mt-4 flex items-center space-x-2 text-[11px] font-mono text-slate-200">
                            <span class="px-2.5 py-0.5 rounded bg-blue-500/15 text-blue-300 border border-blue-500/30 font-medium">{{ $principles->count() }} Prinzipien</span>
                            <span class="px-2.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-medium">{{ $learnings->count() }} Erkenntnisse</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-blue-400 font-semibold group-hover:text-blue-300 transition">
                        Prinzipien &amp; kognitiven Pfad einsehen &rarr;
                    </div>
                </div>

                <!-- AMF Engine Card -->
                <div 
                    onclick="openAmfModal()"
                    class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-purple-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between shadow-md"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-purple-400 uppercase">AMF 1.1 — DIE MODULFABRIK</span>
                            <span class="text-xs text-purple-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-purple-400 transition">Allocore Module Framework 1.1</h3>
                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                            Bevor Module entwickelt werden, definiert das AMF, wie Module entstehen. Es ist die standardisierte Blaupause aus 9 Bausteinen über 5 Allocore-Ebenen (Umsatz bis Vermächtnis).
                        </p>
                        <div class="mt-4 flex flex-wrap gap-1.5 text-[11px] font-mono text-slate-200">
                            <span class="px-2 py-0.5 rounded bg-purple-500/15 text-purple-300 border border-purple-500/30 font-medium">9 Bausteine</span>
                            <span class="px-2 py-0.5 rounded bg-blue-500/15 text-blue-300 border border-blue-500/30 font-medium">5 Ebenen</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-medium">AP-005 konform</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-purple-400 font-semibold group-hover:text-purple-300 transition">
                        AMF 1.1 Blaupause &amp; Modulfabrik einsehen &rarr;
                    </div>
                </div>

                <!-- ARF Engine Card -->
                <div 
                    onclick="openArfModal()"
                    class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-emerald-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between shadow-md"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-emerald-400 uppercase">ARF-ENGINE</span>
                            <span class="text-xs text-emerald-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-emerald-400 transition">Regelkreis-Schließung (Loop Closure)</h3>
                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                            Das Abschließen eines Reviews führt <code class="font-mono text-emerald-300">SpawnLearningFromReview</code> aus und erzeugt automatisch eine neue Erkenntnis (Learning) in ALF für kontinuierliche Evolution.
                        </p>
                        <div class="mt-4 flex items-center space-x-2 text-[11px] font-mono text-slate-200">
                            <span class="px-2.5 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-medium">{{ $reviews->count() }} Reviews</span>
                            <span class="px-2.5 py-0.5 rounded bg-blue-500/15 text-blue-300 border border-blue-500/30 font-medium">Auto-Generierung aktiv</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-emerald-400 font-semibold group-hover:text-emerald-300 transition">
                        Reviews &amp; Regelkreis einsehen &rarr;
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-dark-border py-4 px-6 text-center text-xs text-slate-500">
        &copy; 2026 Disavo Holding GmbH • Disavo Operating System (DOS)
    </footer>

    <!-- ==================== MODALS ==================== -->

    <!-- 1. MODULE DETAIL MODAL -->
    <div id="moduleModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-mono font-bold text-blue-400 uppercase">AMF Module Pillar</span>
                        <span class="text-slate-500">/</span>
                        <span id="mModalSlug" class="text-xs font-mono text-slate-300 font-semibold"></span>
                    </div>
                    <h2 id="mModalTitle" class="text-2xl font-extrabold text-white mt-1"></h2>
                    <p id="mModalDescription" class="text-xs text-slate-300 mt-1 max-w-xl"></p>
                </div>
                <button onclick="closeModal('moduleModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition text-lg font-bold">✕</button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                <!-- Zielzustand (Target Goal) -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-blue-400 font-bold mb-2">1. Zielzustand (Maturity Target State)</h4>
                    <div id="mModalGoal" class="p-4 rounded-xl bg-dark-card border border-dark-border"></div>
                </div>

                <!-- Governing Principles -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-purple-400 font-bold mb-2">2. Active Governing Principles (ALF)</h4>
                    <div id="mModalPrinciples" class="space-y-2"></div>
                </div>

                <!-- KPIs -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-mono uppercase text-emerald-400 font-bold">3. Key Performance Indicators (Metrics)</h4>
                        <button onclick="closeModal('moduleModal'); openKpiModal();" class="text-xs font-bold text-emerald-400 hover:underline">+ Record KPI Reading</button>
                    </div>
                    <div id="mModalKpis" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                </div>

                <!-- Operational Tools -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-mono uppercase text-amber-400 font-bold">4. Operational Tools &amp; Standard Operating Procedures</h4>
                        <button id="mModalAddToolBtn" class="text-xs font-bold text-amber-400 hover:underline">+ Neues Werkzeug entwerfen</button>
                    </div>
                    <div id="mModalTools" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                </div>

                <!-- Recent Audits -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-mono uppercase text-slate-300 font-bold">5. Reifegrad-Audits (Maturity Assessments)</h4>
                        <button id="mModalAuditBtn" class="text-xs font-bold text-blue-400 hover:underline">+ Conduct New Audit</button>
                    </div>
                    <div id="mModalAudits" class="space-y-2"></div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end">
                <button onclick="closeModal('moduleModal')" class="px-5 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-bold text-white transition">
                    Close Details
                </button>
            </div>
        </div>
    </div>

    <!-- 2. QUICK CAPTURE MODAL (Observation / Learning / Question) -->
    <div id="captureModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-xl w-full flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase">ALF Frictionless Capture</span>
                    <h2 class="text-xl font-bold text-white mt-1">⚡ Quick Capture Signal</h2>
                    <p class="text-xs text-slate-300 mt-1">Log an Observation, Learning, or Question in under 5 seconds.</p>
                </div>
                <button onclick="closeModal('captureModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.capture') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Capture Type</label>
                    <select name="type" class="w-full px-3 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none">
                        <option value="observation">Observation (Beobachtung — Real-world field finding)</option>
                        <option value="learning">Learning (Validated takeaway / experience)</option>
                        <option value="question">Question (Offene Frage for investigation)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Title / Headline</label>
                    <input type="text" name="title" required placeholder="e.g. Vertrauliche Kundenkommunikation vor Übergabe" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none placeholder-slate-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Content / Detailed Context</label>
                    <textarea name="content" rows="4" required placeholder="Describe what happened, the context, and key implications..." class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none placeholder-slate-500"></textarea>
                </div>

                <div class="pt-3 border-t border-dark-border flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('captureModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-md shadow-blue-600/20">
                        Save to Knowledge Graph
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. AUDIT EXECUTION MODAL (Questionnaire & Scoring) -->
    <div id="auditModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-emerald-400 uppercase">AMF Reifegrad-Audit</span>
                    <h2 class="text-xl font-bold text-white mt-1">📋 Conduct Module Audit</h2>
                    <p class="text-xs text-slate-300 mt-1">Evaluate questions with weighted scores (1 = Initial, 5 = Fully Optimized).</p>
                </div>
                <button onclick="closeModal('auditModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.audit.submit') }}" id="auditForm" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <input type="hidden" name="module_id" id="auditFormModuleId" value="">
                <input type="hidden" name="audit_template_id" id="auditFormTemplateId" value="">
                <input type="hidden" name="audit_run_id" id="auditFormRunId" value="">

                <div class="p-6 overflow-y-auto space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Module to Audit</label>
                        <select id="auditModuleSelect" onchange="onAuditModuleChanged(this.value)" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-emerald-500 focus:outline-none">
                            @foreach ($modules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dynamic Questionnaire Container -->
                    <div id="auditQuestionsContainer" class="space-y-4 pt-2"></div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('auditModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white transition shadow-md shadow-emerald-600/20">
                        Submit Audit &amp; Calculate Score
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. RECORD KPI READING MODAL -->
    <div id="kpiModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-md w-full flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-purple-400 uppercase">Operational Telemetry</span>
                    <h2 class="text-xl font-bold text-white mt-1">📈 Record Metric Reading</h2>
                    <p class="text-xs text-slate-300 mt-1">Enter latest performance value for module KPIs.</p>
                </div>
                <button onclick="closeModal('kpiModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.kpi.record') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Select Metric</label>
                    <select name="kpi_id" class="w-full px-3 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-purple-500 focus:outline-none">
                        @foreach ($modules as $mod)
                            @foreach ($mod->kpis as $k)
                                <option value="{{ $k->id }}">[{{ $mod->name }}] {{ $k->name }} ({{ $k->unit }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">New Value</label>
                    <input type="number" step="0.01" name="value" required placeholder="e.g. 85.5" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-purple-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Audit Notes / Context</label>
                    <textarea name="notes" rows="2" placeholder="e.g. Q1 audit measurement via internal ERP" class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-purple-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-dark-border flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('kpiModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-xs font-bold text-white transition shadow-md shadow-purple-600/20">
                        Save Metric Reading
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. SCHEDULE REVIEW MODAL -->
    <div id="reviewCreateModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-amber-400 uppercase">ARF Strategic Governance</span>
                    <h2 class="text-xl font-bold text-white mt-1">🔄 Schedule Strategic Review &amp; Audit Questions</h2>
                    <p class="text-xs text-slate-300 mt-1">Review-Zyklus ansetzen und relevante Audit-Fragen &amp; Untersuchungsschwerpunkte zuordnen.</p>
                </div>
                <button onclick="closeModal('reviewCreateModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.review.create') }}" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto space-y-5 flex-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Review Title</label>
                        <input type="text" name="title" required placeholder="e.g. Q2-2026 Holding Governance Review" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Period</label>
                            <input type="text" name="period" required placeholder="Q2-2026" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Review Date</label>
                            <input type="date" name="review_date" required value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Executive Summary / Agenda</label>
                        <textarea name="summary" rows="2" required placeholder="Focus areas, key risks, and development targets to review..." class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none"></textarea>
                    </div>

                    <!-- AUDIT QUESTIONS SECTION -->
                    <div class="pt-4 border-t border-dark-border space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-white flex items-center space-x-2">
                                    <span>📋</span>
                                    <span>Audit-Fragen für dieses Review einreichen</span>
                                </h4>
                                <p class="text-xs text-slate-400 mt-0.5">Erfassen Sie konkrete Fragen, die im Review geprüft werden sollen (Review-Items).</p>
                            </div>
                            <button type="button" onclick="addReviewQuestionRow()" class="px-2.5 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-xs font-bold transition flex items-center space-x-1">
                                <span>+</span>
                                <span>Frage hinzufügen</span>
                            </button>
                        </div>

                        <!-- Dynamic custom question rows container -->
                        <div id="reviewQuestionsContainer" class="space-y-3">
                            <div class="review-question-row p-3.5 rounded-xl bg-dark-card border border-dark-border space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-mono text-amber-400 font-semibold row-number">Audit-Frage 01</span>
                                    <button type="button" onclick="removeReviewQuestionRow(this)" class="text-xs text-slate-500 hover:text-rose-400 transition" title="Frage entfernen">✕</button>
                                </div>
                                <div>
                                    <input type="text" name="questions[]" placeholder="z.B. Wie transparent ist die Nachfolge-Kommunikation für Führungskräfte?" class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-[11px] text-slate-400 whitespace-nowrap">Modul-Zuordnung:</label>
                                    <select name="question_modules[]" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                                        <option value="">(Keine / Übergreifend)</option>
                                        @foreach ($modules as $mod)
                                            <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Pre-existing Audit Questions Picker from canonical templates -->
                        @if ($auditTemplates->isNotEmpty())
                            <details class="group bg-dark-card/60 border border-dark-border rounded-xl overflow-hidden">
                                <summary class="p-3.5 text-xs font-bold text-slate-300 hover:text-white cursor-pointer select-none flex items-center justify-between transition">
                                    <span class="flex items-center space-x-2">
                                        <span class="text-amber-400">⚡</span>
                                        <span>Bestehende Modul-Audit-Fragen übernehmen (Optional)</span>
                                    </span>
                                    <span class="text-slate-500 group-open:rotate-180 transition-transform">▼</span>
                                </summary>
                                <div class="p-4 pt-1 border-t border-dark-border/60 space-y-4 max-h-60 overflow-y-auto text-xs">
                                    <p class="text-[11px] text-slate-400">Wählen Sie Fragen aus den kanonischen Modul-Audits aus, um sie in dieses Review einzubinden:</p>
                                    @foreach ($auditTemplates as $tmpl)
                                        @if ($tmpl->questions && $tmpl->questions->isNotEmpty())
                                            <div class="space-y-1.5">
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                                        {{ $tmpl->module?->name ?? 'Modul' }}
                                                    </span>
                                                    <span class="text-[11px] font-semibold text-slate-300">{{ $tmpl->name }}</span>
                                                </div>
                                                <div class="space-y-1 pl-2">
                                                    @foreach ($tmpl->questions as $tq)
                                                        <label class="flex items-start space-x-2 p-1.5 rounded-lg hover:bg-dark-surface cursor-pointer text-slate-300 hover:text-white transition">
                                                            <input type="checkbox" name="existing_question_ids[]" value="{{ $tq->id }}" class="mt-0.5 rounded bg-dark-surface border-dark-border text-amber-500 focus:ring-0">
                                                            <span class="leading-tight">{{ $tq->question_text }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('reviewCreateModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-xs font-bold text-white transition shadow-md shadow-amber-600/20">
                        Create Review &amp; Save Questions
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. ALF INSPECTION MODAL (Prinzipienschmiede & Lernrahmen) -->
    <div id="alfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase">ALF Prinzipienschmiede</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-purple-400 font-semibold">Kognitive Pipeline</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mt-1">🧠 ALF — Allgemeiner Lernrahmen</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Der 4-Stufen-Weg vom Alltagsproblem zur verbindlichen Unternehmensregel: 
                        <strong class="text-white">Beobachtung &rarr; Erkenntnis (Learning) &rarr; Validierung &rarr; Prinzip (Principle)</strong>
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="closeModal('alfModal'); openCaptureModal();" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm flex items-center space-x-1">
                        <span>⚡</span>
                        <span>Beobachtung erfassen</span>
                    </button>
                    <button onclick="closeModal('alfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
                </div>
            </div>

            <!-- Pipeline Tabs / Navigation -->
            <div class="px-6 pt-3 bg-dark-card/60 border-b border-dark-border flex space-x-4 text-xs font-semibold overflow-x-auto">
                <button onclick="switchAlfTab('principles')" id="alfTabBtnPrinciples" class="pb-3 border-b-2 border-purple-500 text-purple-400 font-bold transition flex items-center space-x-1.5">
                    <span>👑</span>
                    <span>Aktive Prinzipien ({{ $principlesCount }})</span>
                </button>
                <button onclick="switchAlfTab('learnings')" id="alfTabBtnLearnings" class="pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5">
                    <span>💡</span>
                    <span>Erkenntnisse (Learnings: {{ $learnings->count() }})</span>
                </button>
                <button onclick="switchAlfTab('observations')" id="alfTabBtnObservations" class="pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5">
                    <span>👁️</span>
                    <span>Beobachtungs-Pool (Observations: {{ $observations->count() }})</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm flex-1">
                <!-- 1. PRINCIPLES VIEW -->
                <div id="alfSectionPrinciples" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-purple-400 font-bold">Verbindliche Handlungsregeln ({{ $principlesCount }})</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Von Stewards ratifizierte Grundsätze, die operative Module und Entscheidungen steuern.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($principles as $principle)
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border hover:border-purple-500/40 transition">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h5 class="font-bold text-white text-base flex items-center space-x-2">
                                            <span>⚖️</span>
                                            <span>{{ $principle->title }}</span>
                                        </h5>
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[11px] font-mono text-slate-400">
                                            <span class="px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold">v{{ $principle->version }} • {{ $principle->state }}</span>
                                            @if ($principle->created_at)
                                                <span>• Erfasst: {{ $principle->created_at->format('d.m.Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 rounded-lg bg-dark-surface border border-dark-border/60 mt-3 text-xs text-slate-200 italic leading-relaxed">
                                    &ldquo;{{ $principle->statement }}&rdquo;
                                </div>
                                @if ($principle->rationale)
                                    <p class="text-xs text-slate-400 mt-2 pl-1"><strong class="text-slate-300">Rationale:</strong> {{ $principle->rationale }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 rounded-xl bg-dark-card border border-dark-border text-center text-slate-400 text-xs">
                                <p class="text-base font-bold text-slate-300">Noch keine Prinzipien erfasst.</p>
                                <p class="mt-1">Nutzen Sie den Beobachtungs-Pool oder validierte Learnings, um die ersten Unternehmensgrundsätze zu schmieden.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 2. LEARNINGS VIEW -->
                <div id="alfSectionLearnings" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-amber-400 font-bold">Verdichtete Erkenntnisse (Learnings: {{ $learnings->count() }})</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Erfahrungen aus Audits, Reviews und Alltag. Validieren und zum Prinzip erheben:</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @forelse ($learnings as $learning)
                            @php
                                $stateStr = is_object($learning->state) ? $learning->state->name() : (string) $learning->state;
                                $isValidated = str_contains(strtolower($stateStr), 'validated');
                                $isDraft = str_contains(strtolower($stateStr), 'draft');
                                $isPromoted = str_contains(strtolower($stateStr), 'promoted');
                            @endphp
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $isPromoted ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : ($isValidated ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30') }}">
                                                {{ $stateStr }}
                                            </span>
                                            <h5 class="font-bold text-white text-sm">{{ $learning->title }}</h5>
                                        </div>
                                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">{{ $learning->summary }}</p>
                                        @if ($learning->rationale)
                                            <p class="text-[11px] text-slate-400 mt-1 italic pl-1">Rationale: {{ $learning->rationale }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 shrink-0">
                                        @if ($isDraft)
                                            <form method="POST" action="{{ route('actions.alf.validate') }}">
                                                @csrf
                                                <input type="hidden" name="learning_id" value="{{ $learning->id }}">
                                                <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm whitespace-nowrap">
                                                    ✓ Validieren
                                                </button>
                                            </form>
                                        @endif
                                        @if (!$isPromoted)
                                            <button 
                                                onclick="openPromoteLearningModal({{ $learning->id }}, '{{ addslashes($learning->title) }}', '{{ addslashes($learning->summary) }}')"
                                                class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs transition shadow-sm whitespace-nowrap"
                                            >
                                                👑 Zum Prinzip erheben
                                            </button>
                                        @else
                                            <span class="text-[11px] font-mono text-purple-400 font-bold px-2 py-1 text-center">✓ Prinzip aktiv</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 rounded-xl bg-dark-card border border-dark-border text-center text-slate-400 text-xs">
                                <p class="text-base font-bold text-slate-300">Noch keine Learnings erfasst.</p>
                                <p class="mt-1">Verdichten Sie Beobachtungen aus dem Beobachtungs-Pool oder schließen Sie Reviews ab, um automatisch Learnings zu generieren.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- 3. OBSERVATIONS VIEW -->
                <div id="alfSectionObservations" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-blue-400 font-bold">Beobachtungs-Pool (Observations: {{ $observations->count() }})</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Reale Vorkommnisse und Erkenntnis-Quellen. Verdichten Sie ein Vorkommnis zum Learning:</p>
                        </div>
                        <button onclick="closeModal('alfModal'); openCaptureModal();" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition">
                            + Neue Beobachtung
                        </button>
                    </div>
                    <div class="space-y-3">
                        @forelse ($observations as $obs)
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-blue-400 font-bold">👁️</span>
                                        <h5 class="font-bold text-white text-sm">{{ $obs->title }}</h5>
                                        <span class="text-[11px] text-slate-500 font-mono">{{ $obs->created_at?->format('d.m.Y H:i') }}</span>
                                    </div>
                                    <p class="text-xs text-slate-300 leading-relaxed">{{ $obs->content }}</p>
                                </div>
                                <button 
                                    onclick="openSynthesizeLearningModal({{ $obs->id }}, '{{ addslashes($obs->title) }}', '{{ addslashes($obs->content) }}')"
                                    class="px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-xs font-bold transition whitespace-nowrap shrink-0"
                                >
                                    💡 Zu Erkenntnis verdichten
                                </button>
                            </div>
                        @empty
                            <div class="p-8 rounded-xl bg-dark-card border border-dark-border text-center text-slate-400 text-xs">
                                <p class="text-base font-bold text-slate-300">Keine Beobachtungen vorhanden.</p>
                                <p class="mt-1">Erfassen Sie reale Praxiserfahrungen mit dem Schnell-Erfassen-Button.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end">
                <button onclick="closeModal('alfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition">Schließen</button>
            </div>
        </div>
    </div>

    <!-- 7. AMF INSPECTION MODAL (AMF 1.1 — Die Modulfabrik) -->
    <div id="amfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase">
                            Allocore Module Framework 1.1
                        </span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-purple-400 font-semibold">Die Modulfabrik &amp; Standard-Blaupause</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-white mt-1.5">AMF 1.1 — Die Modulfabrik</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-2xl">
                        „Bevor wir Module entwickeln, müssen wir definieren, wie Module entstehen. Das AMF ist die Blaupause für alle zukünftigen Allocore-Module.“
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button 
                        onclick="openToolDesignerModal()" 
                        class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition shadow-sm flex items-center space-x-1.5"
                    >
                        <span>🛠️</span>
                        <span>Neues Werkzeug</span>
                    </button>
                    <button 
                        onclick="closeModal('amfModal'); openModuleCreateModal();" 
                        class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm flex items-center space-x-1.5"
                    >
                        <span>+</span>
                        <span>Neues Modul</span>
                    </button>
                    <button onclick="closeModal('amfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
                </div>
            </div>

            <!-- AMF Navigation Tabs -->
            <div class="px-6 pt-3 bg-dark-card/60 border-b border-dark-border flex space-x-4 text-xs font-semibold overflow-x-auto">
                <button onclick="switchAmfTab('blueprint')" id="amfTabBtnBlueprint" class="pb-3 border-b-2 border-blue-500 text-blue-400 font-bold transition flex items-center space-x-1.5">
                    <span>🏭</span>
                    <span>Standard-Blaupause (9 Bausteine)</span>
                </button>
                <button onclick="switchAmfTab('studio')" id="amfTabBtnStudio" class="pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5">
                    <span>🛠️</span>
                    <span>Werkzeug-Studio ({{ $modules->pluck('tools')->flatten()->count() }} Tools)</span>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 text-sm flex-1">
                <div id="amfSectionBlueprint" class="space-y-6">

                <!-- AMF Purpose & Meta-Factory Banner -->
                <div class="p-5 rounded-2xl bg-[#0d1527] border border-blue-500/40 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-base">🏭</span>
                            <div>
                                <h3 class="text-sm font-bold text-white">Strategische Einordnung: Das AMF ist kein weiteres Modul</h3>
                                <p class="text-[11px] text-blue-300">Es ist die Modulfabrik — alle Anwendungsmodule sind Instanzen dieser Blaupause.</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">
                            AMF 1.1 Standard
                        </span>
                    </div>
                    <p class="text-xs text-slate-200 leading-relaxed">
                        Module wie <strong class="text-white">Markenaufbau</strong>, <strong class="text-white">Unternehmerentwicklung</strong>, <strong class="text-white">Führung</strong>, <strong class="text-white">Vertrieb</strong> oder <strong class="text-white">Nachfolge</strong> sind <em>Anwendungen</em> dieses Frameworks. Das AMF stellt sicher, dass:
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-[11px] text-center">
                        <div class="p-2 rounded-lg bg-dark-surface border border-dark-border text-slate-300">
                            <span class="text-blue-400 font-bold block">1. Konsistenz</span>
                            Alle Module gleich aufgebaut
                        </div>
                        <div class="p-2 rounded-lg bg-dark-surface border border-dark-border text-slate-300">
                            <span class="text-purple-400 font-bold block">2. Reusability</span>
                            Learnings wiederverwendbar
                        </div>
                        <div class="p-2 rounded-lg bg-dark-surface border border-dark-border text-slate-300">
                            <span class="text-emerald-400 font-bold block">3. Vergleich</span>
                            Audits methodisch vergleichbar
                        </div>
                        <div class="p-2 rounded-lg bg-dark-surface border border-dark-border text-slate-300">
                            <span class="text-amber-400 font-bold block">4. Kontrolle</span>
                            Reviews strukturiert möglich
                        </div>
                        <div class="p-2 rounded-lg bg-dark-surface border border-dark-border text-slate-300">
                            <span class="text-rose-400 font-bold block">5. Skalierung</span>
                            Neue Module skalierbar entstehen
                        </div>
                    </div>
                </div>

                <!-- Principle AP-005 Callout -->
                <div class="p-4 rounded-xl bg-gradient-to-r from-purple-950/40 via-dark-card to-blue-950/40 border border-purple-500/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase">
                                Kernprinzip AP-005
                            </span>
                            <span class="text-xs font-bold text-white">„Ein Modul ist kein Audit.“</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1">
                            Ein vollständiges Modul besteht immer aus sechs Elementen: <strong class="text-white">Audit, Messregeln, KPIs, Werkzeugen, Learnings und Reviews</strong>.
                        </p>
                    </div>
                    <div class="flex items-center space-x-1 font-mono text-[11px] text-purple-300 bg-dark-surface/80 px-3 py-1.5 rounded-lg border border-purple-500/30 whitespace-nowrap">
                        <span>Audit + KPI + Tools + ALF + ARF</span>
                    </div>
                </div>

                <!-- The 5 Allocore Levels -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-mono font-bold text-blue-400 uppercase">3. Die 5 Allocore-Ebenen</span>
                            <span class="text-slate-500">•</span>
                            <span class="text-xs text-slate-300">Jedes Modul betrachtet sein Entwicklungsobjekt auf diesen 5 Ebenen</span>
                        </div>
                        <span class="text-[11px] font-mono text-slate-400">Ganzheitliche Diagnose</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-5 gap-2.5">
                        <div class="p-3 rounded-xl bg-dark-card border border-blue-500/30 hover:border-blue-500 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs text-blue-400 font-bold">Ebene 1</span>
                                <span class="text-xs">📈</span>
                            </div>
                            <h5 class="font-bold text-white text-xs">Umsatz</h5>
                            <p class="text-[11px] text-slate-400 mt-1">Wachstum, Erlöse, Marktdurchdringung &amp; Lead-Generierung.</p>
                        </div>

                        <div class="p-3 rounded-xl bg-dark-card border border-emerald-500/30 hover:border-emerald-500 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs text-emerald-400 font-bold">Ebene 2</span>
                                <span class="text-xs">💰</span>
                            </div>
                            <h5 class="font-bold text-white text-xs">Gewinn</h5>
                            <p class="text-[11px] text-slate-400 mt-1">Margen, Rentabilität, Kapitaleffizienz &amp; ROCE.</p>
                        </div>

                        <div class="p-3 rounded-xl bg-dark-card border border-purple-500/30 hover:border-purple-500 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs text-purple-400 font-bold">Ebene 3</span>
                                <span class="text-xs">⚙️</span>
                            </div>
                            <h5 class="font-bold text-white text-xs">Ordnung</h5>
                            <p class="text-[11px] text-slate-400 mt-1">Prozesse, Governance, Rollen, SOPs &amp; Skalierbarkeit.</p>
                        </div>

                        <div class="p-3 rounded-xl bg-dark-card border border-amber-500/30 hover:border-amber-500 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs text-amber-400 font-bold">Ebene 4</span>
                                <span class="text-xs">👑</span>
                            </div>
                            <h5 class="font-bold text-white text-xs">Einfluss</h5>
                            <p class="text-[11px] text-slate-400 mt-1">Marktposition, Brand Authority, Vertrauen &amp; Preissetzungsmacht.</p>
                        </div>

                        <div class="p-3 rounded-xl bg-dark-card border border-rose-500/30 hover:border-rose-500 transition">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs text-rose-400 font-bold">Ebene 5</span>
                                <span class="text-xs">🏛️</span>
                            </div>
                            <h5 class="font-bold text-white text-xs">Vermächtnis</h5>
                            <p class="text-[11px] text-slate-400 mt-1">Nachhaltigkeit, Generationsfähigkeit &amp; Inhaber-Unabhängigkeit.</p>
                        </div>
                    </div>
                </div>

                <!-- The 9 Standard Building Blocks of AMF 1.1 -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-blue-400 font-bold">Die 9 Standard-Bausteine jedes Moduls (AMF 1.1)</h4>
                            <span class="text-[11px] text-slate-400">Verbindliche Struktur für jedes Allocore-Modul vor der Implementierung</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-300">Baustein 01 bis 09</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                        <!-- Block 1 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-blue-400">01. Entwicklungsobjekt</span>
                                <span class="text-[10px] text-slate-400 font-mono">Fokus</span>
                            </div>
                            <strong class="text-white block text-sm">Was soll entwickelt werden?</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Klare Definition: Unternehmen, Unternehmer, Marke, Vertrieb, Führung, Nachfolge oder Unternehmensgruppe.
                            </p>
                        </div>

                        <!-- Block 2 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-emerald-400">02. Zieldefinition</span>
                                <span class="text-[10px] text-slate-400 font-mono">Messbar</span>
                            </div>
                            <strong class="text-white block text-sm">Wann gilt es als erfolgreich?</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Beispiel Marke: Nicht nur „Reichweite“, sondern Wiedererkennung, Vertrauen und echte Preissetzungsmacht.
                            </p>
                        </div>

                        <!-- Block 3 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-purple-400">03. 5 Allocore-Ebenen</span>
                                <span class="text-[10px] text-slate-400 font-mono">Dimensionen</span>
                            </div>
                            <strong class="text-white block text-sm">Mehrdimensionale Betrachtung</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Systematische Diagnose über: Umsatz, Gewinn, Ordnung, Einfluss und Vermächtnis.
                            </p>
                        </div>

                        <!-- Block 4 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-amber-400">04. Strukturiertes Audit</span>
                                <span class="text-[10px] text-slate-400 font-mono">Reifegrad</span>
                            </div>
                            <strong class="text-white block text-sm">Kernfragen &amp; Kriterien</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Gewichtete Kernfragen, Definitionen und Bewertungskriterien pro Ebene zur Ermittlung des Reifegrad-Deltas.
                            </p>
                        </div>

                        <!-- Block 5 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-blue-400">05. KPI-System</span>
                                <span class="text-[10px] text-slate-400 font-mono">Messregel</span>
                            </div>
                            <strong class="text-white block text-sm">Keine Frage ohne Messregel</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Jede Kennzahl definiert Name, Definition, Datenquelle, Zielwert, Ist-Wert und Trend-Richtung.
                            </p>
                        </div>

                        <!-- Block 6 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-purple-400">06. Werkzeuge (Tools)</span>
                                <span class="text-[10px] text-slate-400 font-mono">Lösungsbibl.</span>
                            </div>
                            <strong class="text-white block text-sm">Operative Befähigung</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Praxis-Tools: Wissen, Checklisten, Templates, Whitepaper, SaaS-Tools und KI-Funktionen.
                            </p>
                        </div>

                        <!-- Block 7 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-emerald-400">07. Learnings (ALF)</span>
                                <span class="text-[10px] text-slate-400 font-mono">Erfahrung</span>
                            </div>
                            <strong class="text-white block text-sm">Empirische Rückkopplung</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Kognitiver Pfad: <code class="text-emerald-300 font-mono">Modul → Beobachtung → Learning → Prinzip</code>.
                            </p>
                        </div>

                        <!-- Block 8 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-amber-400">08. Review-Zyklus (ARF)</span>
                                <span class="text-[10px] text-slate-400 font-mono">Regelkreis</span>
                            </div>
                            <strong class="text-white block text-sm">Regelmäßige Überprüfung</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Modul-Owner, Review-Rhythmus, nächste Prüfung und automatisierte Loop-Closure.
                            </p>
                        </div>

                        <!-- Block 9 -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-rose-400">09. Versionierung</span>
                                <span class="text-[10px] text-slate-400 font-mono">Evolution</span>
                            </div>
                            <strong class="text-white block text-sm">Reifegrad-Zyklen</strong>
                            <p class="text-slate-300 text-[11px] leading-relaxed">
                                Klare Reifegrade: <code class="text-white font-mono">v0.1 Konzept → v0.2 Pilot → v1.0 Produktiv → v2.0 Skaliert</code>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Standard-Lebenszyklus eines Moduls (Visual Diagram) -->
                <div class="p-4 rounded-xl bg-[#0b1329] border border-blue-500/30">
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase block mb-2">Standard-Lebenszyklus eines Moduls</span>
                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-mono">
                        <span class="px-2 py-1 rounded bg-slate-800 text-slate-200 border border-slate-700">1. Beobachtung</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-slate-800 text-slate-200 border border-slate-700">2. Problem erkannt</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-slate-800 text-slate-200 border border-slate-700">3. Learning</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-blue-900/60 text-blue-200 border border-blue-500/40">4. Modul-Idee</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-blue-900/60 text-blue-200 border border-blue-500/40">5. AMF-Struktur</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-purple-900/60 text-purple-200 border border-purple-500/40">6. Audit</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-purple-900/60 text-purple-200 border border-purple-500/40">7. KPIs</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-emerald-900/60 text-emerald-200 border border-emerald-500/40">8. Werkzeuge</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-emerald-900/60 text-emerald-200 border border-emerald-500/40">9. Anwendung</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-amber-900/60 text-amber-200 border border-amber-500/40">10. Review</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-amber-900/60 text-amber-200 border border-amber-500/40">11. Verbesserung</span>
                        <span class="text-blue-400">&rarr;</span>
                        <span class="px-2 py-1 rounded bg-rose-900/60 text-rose-200 border border-rose-500/40">12. Neue Version</span>
                    </div>
                </div>

                <!-- Modul-Entscheidungsmatrix (Decision Framework) -->
                <div class="p-5 rounded-2xl bg-[#0f172a] border border-blue-500/30 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-sm">🧭</span>
                            <div>
                                <h4 class="text-sm font-bold text-white">AMF-Entscheidungsmatrix: Neues Modul nötig?</h4>
                                <span class="text-[11px] text-slate-400">Methodische Bedarfsprüfung vor der Erstellung neuer Unternehmensbereiche</span>
                            </div>
                        </div>
                        <span id="decisionCountBadge" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-800 text-slate-300 border border-dark-border">
                            0 / 4 Kriterien erfüllt
                        </span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        Ein AMF-Modul ist kein temporäres Projekt, sondern eine dauerhafte organisatorische Säule. Prüfen Sie die 4 Kriterien:
                    </p>

                    <div class="space-y-2 text-xs">
                        <label class="flex items-start space-x-3 p-2.5 rounded-xl bg-dark-card border border-dark-border/80 hover:border-blue-500/40 cursor-pointer transition">
                            <input type="checkbox" class="amf-criteria mt-0.5 rounded bg-dark-surface border-dark-border text-blue-600 focus:ring-0" onchange="evaluateAmfCriteria()">
                            <div>
                                <strong class="text-white block font-medium">1. Dauerhafte Führungs- &amp; Steuerungsaufgabe</strong>
                                <span class="text-slate-400">Handelt es sich um eine langfristige Kernkompetenz des Unternehmens und nicht um ein befristetes Einzelprojekt?</span>
                            </div>
                        </label>

                        <label class="flex items-start space-x-3 p-2.5 rounded-xl bg-dark-card border border-dark-border/80 hover:border-blue-500/40 cursor-pointer transition">
                            <input type="checkbox" class="amf-criteria mt-0.5 rounded bg-dark-surface border-dark-border text-blue-600 focus:ring-0" onchange="evaluateAmfCriteria()">
                            <div>
                                <strong class="text-white block font-medium">2. Eindeutiger Zielzustand &amp; Reifegrad messbar</strong>
                                <span class="text-slate-400">Kann ein quantifizierbarer Zielzustand (Zielzustand mit Reifegrad-Audits 0–100 %) formuliert werden?</span>
                            </div>
                        </label>

                        <label class="flex items-start space-x-3 p-2.5 rounded-xl bg-dark-card border border-dark-border/80 hover:border-blue-500/40 cursor-pointer transition">
                            <input type="checkbox" class="amf-criteria mt-0.5 rounded bg-dark-surface border-dark-border text-blue-600 focus:ring-0" onchange="evaluateAmfCriteria()">
                            <div>
                                <strong class="text-white block font-medium">3. Eigene Governance, KPIs &amp; Werkzeuge erforderlich</strong>
                                <span class="text-slate-400">Werden dedizierte Kennzahlen (KPIs), Leitprinzipien und Standard Operating Procedures (SOPs) benötigt?</span>
                            </div>
                        </label>

                        <label class="flex items-start space-x-3 p-2.5 rounded-xl bg-dark-card border border-dark-border/80 hover:border-blue-500/40 cursor-pointer transition">
                            <input type="checkbox" class="amf-criteria mt-0.5 rounded bg-dark-surface border-dark-border text-blue-600 focus:ring-0" onchange="evaluateAmfCriteria()">
                            <div>
                                <strong class="text-white block font-medium">4. Empirischer Handlungsbedarf im ALF belegt</strong>
                                <span class="text-slate-400">Liegen wiederkehrende Beobachtungen (Observations) oder Review-Defizite (ARF) vor, die eine Lücke belegen?</span>
                            </div>
                        </label>
                    </div>

                    <!-- Dynamic Decision Recommendation -->
                    <div id="amfDecisionBox" class="p-3.5 rounded-xl bg-slate-900 border border-slate-700 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="text-slate-300">
                            <span class="font-bold text-amber-400" id="amfDecisionTitle">Empfehlung: Kriterien prüfen</span>
                            <p class="text-[11px] text-slate-400 mt-0.5" id="amfDecisionText">Wählen Sie oben die zutreffenden Kriterien aus, um eine methodische Empfehlung zu erhalten.</p>
                        </div>
                        <button 
                            id="amfDecisionBtn"
                            onclick="closeModal('amfModal'); openModuleCreateModal();" 
                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm whitespace-nowrap"
                        >
                            + Neues Modul jetzt entwickeln
                        </button>
                    </div>
                </div>

                <!-- The Canonical Application Modules (Anwendungen der Blaupause) -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-blue-400 font-bold">Aktive Anwendungsmodule der Holding</h4>
                            <span class="text-[11px] text-slate-400">Praktische Implementierungen der AMF 1.1 Blaupause im System</span>
                        </div>
                        <span class="text-xs font-mono text-slate-300 font-bold">{{ count($modules) }} Module aktiv</span>
                    </div>

                    <div class="space-y-3">
                        @foreach ($modules as $mod)
                            @php
                                $modAudit = $mod->auditRuns->sortByDesc('completed_at')->first();
                                $modKpi = $mod->kpis->first();
                                $modReading = $modKpi?->latestReading();
                            @endphp
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border hover:border-blue-500/50 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono text-xs text-blue-400 font-bold">Modul 0{{ $loop->iteration }}</span>
                                        <span class="text-slate-500">•</span>
                                        <span class="font-bold text-white text-sm">{{ $mod->name }}</span>
                                        @if ($modAudit && $modAudit->overall_score !== null)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">
                                                Reifegrad: {{ number_format($modAudit->overall_score, 0) }}%
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-300 leading-relaxed">{{ $mod->description }}</p>
                                </div>
                                <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                                    <button 
                                        onclick="closeModal('amfModal'); openModuleModal({{ $mod->id }})" 
                                        class="px-3 py-1.5 rounded-lg bg-dark-surface hover:bg-dark-hover border border-dark-border text-slate-200 hover:text-white font-semibold text-xs transition whitespace-nowrap"
                                    >
                                        Details &rarr;
                                    </button>
                                    <button 
                                        onclick="closeModal('amfModal'); openAuditModalForModule({{ $mod->id }})" 
                                        class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-sm whitespace-nowrap"
                                    >
                                        📋 Audit starten
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                </div>

                <!-- AMF TOOL-STUDIO TAB -->
                <div id="amfSectionStudio" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-amber-400 font-bold">Operative Werkzeuge &amp; Standard Operating Procedures</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Praxisnahe Hilfsmittel (Checklisten, SOPs, Vorlagen, KI-Prompts) geordnet nach Entwicklungsmodulen:</p>
                        </div>
                        <button onclick="openToolDesignerModal()" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition">
                            + Neues Werkzeug entwerfen
                        </button>
                    </div>

                    @php
                        $allTools = $modules->pluck('tools')->flatten();
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse ($allTools as $tool)
                            @php
                                $toolMod = $modules->firstWhere('id', $tool->module_id);
                            @endphp
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border hover:border-amber-500/40 transition flex flex-col justify-between space-y-3">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-500/20 text-blue-300 border border-blue-500/30 font-semibold truncate">
                                            {{ $toolMod ? $toolMod->name : 'Modul' }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                            {{ $tool->type }}
                                        </span>
                                    </div>
                                    <h5 class="font-bold text-white text-sm">{{ $tool->name }}</h5>
                                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">{{ $tool->description }}</p>
                                </div>
                                <div class="pt-2 border-t border-dark-border/60 flex justify-end">
                                    <button 
                                        onclick="closeModal('amfModal'); openToolViewer({{ $tool->id }})"
                                        class="px-3 py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-xs font-bold transition flex items-center space-x-1.5"
                                    >
                                        <span>⚡</span>
                                        <span>Öffnen / Ausführen &rarr;</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-8 rounded-xl bg-dark-card border border-dark-border text-center text-slate-400 text-xs">
                                <p class="text-base font-bold text-slate-300">Noch keine Werkzeuge erfasst.</p>
                                <p class="mt-1">Entwerfen Sie operative Checklisten, SOPs oder Prompts für Ihre Module.</p>
                                <button onclick="openToolDesignerModal()" class="mt-3 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition shadow-sm inline-block">
                                    + Jetzt Werkzeug anlegen
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end">
                <button onclick="closeModal('amfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition">Schließen</button>
            </div>
        </div>
    </div>

    <!-- 8. ARF INSPECTION MODAL (Audit-Center & Reviews) -->
    <div id="arfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase">ARF Audit-Center &amp; Reviews</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-emerald-400 font-semibold">Kontinuierliche Verbesserung</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mt-1">🎯 ARF — Allocore Review Framework</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Spezielle Audits entwerfen, terminieren, zuweisen und Reifegrade systematisch monitoren.
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="openAmarAuditModal()" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-md shadow-blue-600/30 flex items-center space-x-1">
                        <span>⚡</span>
                        <span>AMAR Master-Audit</span>
                    </button>
                    <button onclick="openAuditScheduleModal()" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm flex items-center space-x-1">
                        <span>📅</span>
                        <span>Audit ansetzen</span>
                    </button>
                    <button onclick="openAuditDesignerModal()" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center space-x-1">
                        <span>🛠️</span>
                        <span>Audit entwerfen</span>
                    </button>
                    <button onclick="closeModal('arfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
                </div>
            </div>

            <!-- ARF Navigation Tabs -->
            <div class="px-6 pt-3 bg-dark-card/60 border-b border-dark-border flex space-x-4 text-xs font-semibold overflow-x-auto">
                <button onclick="switchArfTab('scheduled')" id="arfTabBtnScheduled" class="pb-3 border-b-2 border-emerald-500 text-emerald-400 font-bold transition flex items-center space-x-1.5">
                    <span>📅</span>
                    <span>Geplante Audits ({{ $auditRuns->where('status', 'scheduled')->count() }})</span>
                </button>
                <button onclick="switchArfTab('history')" id="arfTabBtnHistory" class="pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5">
                    <span>📈</span>
                    <span>Reifegrad-Historie ({{ $auditRuns->where('status', 'completed')->count() }})</span>
                </button>
                <button onclick="switchArfTab('reviews')" id="arfTabBtnReviews" class="pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5">
                    <span>🔄</span>
                    <span>Strategische Reviews ({{ $reviews->count() }})</span>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm flex-1">
                <!-- TAB 1: SCHEDULED AUDITS -->
                <div id="arfSectionScheduled" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-emerald-400 font-bold">Geplante &amp; Wiederkehrende Reifegrad-Prüfungen</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Zugewiesene Audits mit Fristen und Rhythmus zur kontinuierlichen Selbstkontrolle:</p>
                        </div>
                        <button onclick="openAuditScheduleModal()" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                            + Audit planen
                        </button>
                    </div>

                    <!-- AMAR Banner -->
                    <div class="p-4 rounded-xl bg-gradient-to-r from-[#0d1b3e] to-[#1a153b] border border-blue-500/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-500/30 text-blue-200 border border-blue-400/40 uppercase">Kanonisches Master-Audit</span>
                                <span class="text-sm font-bold text-white">ALLOCORE MASTER AUDIT (AMAR)</span>
                            </div>
                            <p class="text-xs text-slate-300 max-w-2xl">
                                Ganzheitliche Reifegradprüfung über alle 10 Allocore-Bereiche inklusive Unternehmer-Test, Insight-Protokoll und kybernetischem Regelkreis.
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 shrink-0">
                            <button onclick="openAmarAuditModal()" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-md shadow-blue-600/30 flex items-center space-x-1.5 whitespace-nowrap">
                                <span>⚡</span>
                                <span>AMAR jetzt durchführen</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @php
                            $scheduledRuns = $auditRuns->where('status', 'scheduled');
                        @endphp
                        @forelse ($scheduledRuns as $run)
                            @php
                                $isOverdue = $run->due_date && $run->due_date->isPast();
                            @endphp
                            <div class="p-4 rounded-xl bg-dark-card border {{ $isOverdue ? 'border-rose-500/50' : 'border-dark-border' }} hover:border-emerald-500/40 transition space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            @if ($isOverdue)
                                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                                    ⚠️ Überfällig
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                    Geplant
                                                </span>
                                            @endif
                                            <h5 class="font-bold text-white text-base">{{ $run->title ?: ($run->template ? $run->template->name : 'Reifegrad-Audit') }}</h5>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 mt-1.5 font-mono">
                                            @if ($run->module)
                                                <span class="text-blue-400 font-semibold">Modul: {{ $run->module->name }}</span>
                                                <span>•</span>
                                            @endif
                                            @if ($run->auditor)
                                                <span class="text-purple-300">Prüfer: {{ $run->auditor->name }}</span>
                                                <span>•</span>
                                            @endif
                                            @if ($run->cadence)
                                                <span class="text-amber-400">Rhythmus: {{ ucfirst($run->cadence) }}</span>
                                                <span>•</span>
                                            @endif
                                            @if ($run->due_date)
                                                <span class="{{ $isOverdue ? 'text-rose-400 font-bold' : 'text-slate-300' }}">
                                                    Fällig: {{ $run->due_date->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($run->notes)
                                            <p class="text-xs text-slate-300 mt-2 italic bg-dark-surface/60 p-2 rounded-lg border border-dark-border/60">
                                                Hinweise: {{ $run->notes }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="shrink-0">
                                        <button 
                                            onclick="startScheduledAudit({{ $run->id }}, {{ $run->module_id }}, {{ $run->audit_template_id }})"
                                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/20 whitespace-nowrap flex items-center space-x-1.5"
                                        >
                                            <span>📋</span>
                                            <span>Audit jetzt durchführen &rarr;</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 rounded-xl bg-dark-card border border-dark-border text-center text-slate-400 text-xs">
                                <p class="text-base font-bold text-slate-300">Keine anstehenden Audits terminiert.</p>
                                <p class="mt-1">Setzen Sie Ihr erstes Audit an, um Selbstkontrollen automatisiert zu überwachen.</p>
                                <button onclick="openAuditScheduleModal()" class="mt-3 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm inline-block">
                                    + Jetzt Audit ansetzen
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB 2: AUDIT HISTORY & CONTINUOUS IMPROVEMENT -->
                <div id="arfSectionHistory" class="space-y-4 hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-mono uppercase text-blue-400 font-bold">Reifegrad-Historie &amp; Kontinuierliche Selbstkontrolle</h4>
                            <p class="text-xs text-slate-400 mt-0.5">Ergebnisse abgeschlossener Audits mit automatischer Reifegrad-Messung:</p>
                        </div>
                        <button onclick="openAuditDesignerModal()" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition">
                            + Spezial-Audit entwerfen
                        </button>
                    </div>

                    <div class="space-y-3">
                        @php
                            $completedRuns = $auditRuns->where('status', 'completed');
                        @endphp
                        @forelse ($completedRuns as $cRun)
                            @php
                                $score = $cRun->overall_score ?? 0;
                                $scoreColor = $score >= 80 ? 'text-emerald-400 border-emerald-500/40 bg-emerald-500/10' : ($score >= 60 ? 'text-amber-400 border-amber-500/40 bg-amber-500/10' : 'text-rose-400 border-rose-500/40 bg-rose-500/10');
                            @endphp
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if ($cRun->isAmar())
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/40">
                                                    👑 AMAR Master-Audit
                                                </span>
                                            @endif
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold border {{ $scoreColor }}">
                                                {{ $score }}% {{ $cRun->isAmar() ? 'Allocore Value Score' : 'Reifegrad' }}
                                            </span>
                                            @if ($cRun->isAmar() && $cRun->hasFeatureBan())
                                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40 animate-pulse">
                                                    🚨 Feature Ban aktiv
                                                </span>
                                            @elseif ($cRun->isAmar())
                                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                                    ✓ System wertvoller
                                                </span>
                                            @endif
                                            @if ($cRun->entrepreneur_test_passed)
                                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                    ✓ Unternehmer-Test
                                                </span>
                                            @endif
                                        </div>
                                        <h5 class="font-bold text-white text-base mt-1">{{ $cRun->title ?: ($cRun->template ? $cRun->template->name : 'Reifegrad-Audit') }}</h5>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 mt-1.5 font-mono">
                                            @if ($cRun->module)
                                                <span class="text-blue-400 font-semibold">Modul: {{ $cRun->module->name }}</span>
                                                <span>•</span>
                                            @endif
                                            @if ($cRun->auditor)
                                                <span class="text-purple-300">Auditor: {{ $cRun->auditor->name }}</span>
                                                <span>•</span>
                                            @endif
                                            @if ($cRun->version)
                                                <span class="text-slate-300">v{{ $cRun->version }}</span>
                                                <span>•</span>
                                            @endif
                                            <span>Abgeschlossen: {{ $cRun->completed_at ? $cRun->completed_at->format('d.m.Y H:i') : $cRun->created_at?->format('d.m.Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center space-x-2">
                                        @if ($cRun->isAmar() || !empty($cRun->meta))
                                            <button 
                                                onclick="openAmarDetailModal({{ $cRun->id }})" 
                                                class="px-3.5 py-1.5 rounded-lg bg-blue-600/30 hover:bg-blue-600/50 text-blue-200 border border-blue-500/40 text-xs font-bold transition flex items-center space-x-1"
                                            >
                                                <span>🔍</span>
                                                <span>AMAR-Protokoll ansehen</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @if ($cRun->responses && $cRun->responses->isNotEmpty())
                                    <div class="pt-2 border-t border-dark-border/60">
                                        <span class="text-[11px] font-mono text-slate-400 font-bold uppercase block mb-1.5">Beantwortete Fragen ({{ $cRun->responses->count() }}):</span>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                            @foreach ($cRun->responses as $resp)
                                                <div class="p-2 rounded-lg bg-dark-surface border border-dark-border/80 flex items-center justify-between">
                                                    <span class="text-slate-300 truncate mr-2">{{ $resp->question ? $resp->question->question_text : 'Frage' }}</span>
                                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-800 text-emerald-400 shrink-0">
                                                        {{ $resp->score }}/5
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 rounded-xl bg-dark-card border border-dark-border text-center text-slate-400 text-xs">
                                <p class="text-base font-bold text-slate-300">Noch keine abgeschlossenen Audits.</p>
                                <p class="mt-1">Führen Sie Audits durch, um Reifegrade und Verbesserungstrends festzuhalten.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- TAB 3: STRATEGIC REVIEWS -->
                <div id="arfSectionReviews" class="space-y-4 hidden">
                    <!-- Loop Closure Explanation Banner -->
                    <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40">
                        <h4 class="text-xs font-mono font-bold text-emerald-400 uppercase mb-1">Automatisierte Regelkreis-Schließung:</h4>
                        <p class="text-xs text-slate-200 mt-1">
                            Sobald ein Review als <strong class="text-white font-bold">Closed</strong> markiert wird, führt DOS <code class="font-mono text-emerald-300">CloseReview</code> &rarr; <code class="font-mono text-emerald-300">SpawnLearningFromReview</code> aus.
                        </p>
                        <p class="text-xs text-slate-400 mt-1">
                            Dies erzeugt automatisch eine neue Entwurfs-Erkenntnis (<code class="text-slate-200">Draft Learning</code>) in ALF, verknüpft über die Kante <code class="font-mono text-emerald-300">Review -(generates)-&gt; Learning</code>.
                        </p>
                    </div>

                    <!-- Reviews List -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-mono uppercase text-slate-300 font-bold">Erfasste strategische Reviews ({{ $reviews->count() }})</h4>
                            <button onclick="closeModal('arfModal'); openReviewCreateModal();" class="text-xs font-bold text-emerald-400 hover:underline">+ Neues Review ansetzen</button>
                        </div>

                        <div class="space-y-3">
                            @forelse ($reviews as $review)
                                <div class="p-5 rounded-xl bg-dark-card border border-dark-border">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h5 class="font-bold text-white text-base">{{ $review->title }}</h5>
                                            <span class="text-xs text-slate-400 font-mono">Periode: {{ $review->period }} • Datum: {{ $review->review_date?->format('d.m.Y') }}</span>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase {{ $review->status === 'closed' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/15 text-amber-400 border border-amber-500/30' }}">
                                            {{ $review->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-200 mt-2">{{ $review->summary }}</p>

                                    @if ($review->items && $review->items->isNotEmpty())
                                        <div class="mt-3 pt-3 border-t border-dark-border/60">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[11px] font-mono text-amber-400 font-bold uppercase">Zugeordnete Audit-Fragen &amp; Untersuchungsschwerpunkte ({{ $review->items->count() }}):</span>
                                            </div>
                                            <div class="space-y-1.5">
                                                @foreach ($review->items as $item)
                                                    <div class="p-2.5 rounded-lg bg-dark-surface/80 border border-dark-border/80 flex items-start justify-between gap-2 text-xs">
                                                        <div class="space-y-0.5">
                                                            <div class="flex items-center space-x-2">
                                                                @if ($item->module)
                                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-blue-500/20 text-blue-300 border border-blue-500/30 font-semibold">
                                                                        {{ $item->module->name }}
                                                                    </span>
                                                                @else
                                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-slate-700 text-slate-300 font-semibold">
                                                                        Übergreifend
                                                                    </span>
                                                                @endif
                                                                <span class="text-white font-medium">{{ $item->topic }}</span>
                                                            </div>
                                                            @if ($item->notes)
                                                                <p class="text-[11px] text-slate-400 italic pl-1">{{ $item->notes }}</p>
                                                            @endif
                                                        </div>
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono uppercase bg-slate-800 text-slate-300 border border-slate-700 whitespace-nowrap">
                                                            {{ $item->status }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($review->status !== 'closed')
                                        <div class="mt-4 pt-3 border-t border-dark-border flex justify-end">
                                            <form method="POST" action="{{ route('actions.review.close', $review->id) }}" onsubmit="const btn = this.querySelector('button[type=submit]'); if (btn) { btn.disabled = true; btn.innerHTML = '⏳ Wird abgeschlossen...'; }">
                                                @csrf
                                                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                                    ✓ Review abschließen &amp; Regelkreis auslösen
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-500">Keine Reviews erfasst.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end">
                <button onclick="closeModal('arfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition">Schließen</button>
            </div>
        </div>
    </div>

    <!-- 7. ADD USER / TEAM MEMBER MODAL -->
    <div id="userModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-500/20 text-indigo-400 font-bold flex items-center justify-center text-base">
                        👥
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Add Team Member / New User</h3>
                        <p class="text-xs text-slate-300">Create user account &amp; assign access role to {{ $tenant->name ?? 'Organization' }}</p>
                    </div>
                </div>
                <button onclick="closeModal('userModal')" class="text-slate-400 hover:text-white text-lg font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.user.create') }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Full Name <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        required 
                        placeholder="e.g. Sarah Connor"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:outline-none focus:border-indigo-500 transition"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Email Address <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="sarah@disavo.de"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:outline-none focus:border-indigo-500 transition"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Password <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        value="secret123"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:outline-none focus:border-indigo-500 transition font-mono"
                    >
                    <span class="text-[11px] text-slate-400 mt-1 block">Pre-filled default: <code class="font-mono text-indigo-300">secret123</code> (minimum 8 characters).</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Organizational Role <span class="text-rose-400">*</span>
                    </label>
                    <select 
                        name="role" 
                        required 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:outline-none focus:border-indigo-500 transition"
                    >
                        <option value="steward" selected>Steward — Knowledge Steward &amp; Audit Conductor</option>
                        <option value="contributor">Contributor — Operational Feedback &amp; Capture Contributor</option>
                        <option value="owner">Owner — Full Strategic Governance &amp; Administration</option>
                        <option value="observer">Observer — Read-Only Governance Visibility</option>
                    </select>
                </div>

                <div class="pt-2 border-t border-dark-border flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="closeModal('userModal')" 
                        class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition shadow-md shadow-indigo-600/20"
                    >
                        ✓ Create User &amp; Assign Role
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 8. MODULE CREATE MODAL (AMF 1.1 — Neues Modul nach Blaupause entwickeln) -->
    <div id="moduleCreateModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <!-- Header -->
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase">
                            AMF 1.1 Modulfabrik
                        </span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-purple-400 font-semibold">Standardisierte Blaupause</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mt-1">🧩 Neues Modul nach AMF 1.1 entwickeln</h2>
                    <p class="text-xs text-slate-300 mt-0.5">Definieren Sie Entwicklungsobjekt, messbare Zieldefinition und Allocore-Ebene für eine neue Säule.</p>
                </div>
                <button onclick="closeModal('moduleCreateModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.module.create') }}" class="p-6 overflow-y-auto space-y-4 text-sm">
                @csrf

                <!-- Baustein 1: Entwicklungsobjekt -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            1. Entwicklungsobjekt <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            name="development_object" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                        >
                            <option value="Unternehmen">Unternehmen (Organisation &amp; Skalierung)</option>
                            <option value="Unternehmer">Unternehmer (Führungskompetenz &amp; Rolle)</option>
                            <option value="Marke" selected>Marke (Positionierung &amp; Preissetzungsmacht)</option>
                            <option value="Vertrieb">Vertrieb (Pipeline, Abschluss &amp; Skalierung)</option>
                            <option value="Führung">Führung (Teamautonomie &amp; Delegation)</option>
                            <option value="Nachfolge">Nachfolge (Generationswechsel &amp; Übergabe)</option>
                            <option value="Unternehmensgruppe">Unternehmensgruppe (Portfolio-Holding)</option>
                            <option value="Individuell">Individuelles Entwicklungsobjekt</option>
                        </select>
                        <span class="text-[11px] text-slate-400 mt-0.5 block">Was genau soll methodisch entwickelt werden?</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Modulname <span class="text-rose-400">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            required 
                            placeholder="z. B. Markenaufbau &amp; Marktdifferenzierung" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                        >
                        <span class="text-[11px] text-slate-400 mt-0.5 block">Klarer Name des neuen Systemmoduls.</span>
                    </div>
                </div>

                <!-- Baustein 2: Zieldefinition -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                        2. Messbare Zieldefinition (Wann gilt die Entwicklung als erfolgreich?) <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="target_title" 
                        required 
                        placeholder="z. B. Nicht nur Reichweite, sondern Wiedererkennung, Vertrauen und Preissetzungsmacht" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                    >
                    <span class="text-[11px] text-slate-400 mt-0.5 block">Quantifizierbares oder qualitativ messbares Zielergebnis (nicht nur oberflächliche Metriken).</span>
                </div>

                <!-- Baustein 3: Die 5 Allocore-Ebenen & Reifegrad -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            3. Primäre Allocore-Ebene <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            name="allocore_level" 
                            required 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                        >
                            <option value="Ebene 1: Umsatz">Ebene 1: Umsatz (Wachstum, Erlöse)</option>
                            <option value="Ebene 2: Gewinn">Ebene 2: Gewinn (Margen, Rentabilität, ROCE)</option>
                            <option value="Ebene 3: Ordnung">Ebene 3: Ordnung (Prozesse, SOPs, Governance)</option>
                            <option value="Ebene 4: Einfluss" selected>Ebene 4: Einfluss (Marktposition, Brand Authority)</option>
                            <option value="Ebene 5: Vermächtnis">Ebene 5: Vermächtnis (Nachhaltigkeit, Generation)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Ziel-Reifegrad (Soll in %)
                        </label>
                        <input 
                            type="number" 
                            name="target_score" 
                            min="1" 
                            max="100" 
                            value="85" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none font-mono"
                        >
                    </div>
                </div>

                <!-- Baustein 4: Strukturiertes Audit -->
                <div class="p-4 rounded-xl bg-dark-card/70 border border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-300 uppercase">
                            4. Strukturiertes Audit (Kernfragen &amp; Kriterien)
                        </label>
                        <span class="text-[10px] font-mono font-bold text-blue-400 uppercase">Reifegrad-Messung</span>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Initiale Audit-Kernfrage</label>
                        <input 
                            type="text" 
                            name="audit_question" 
                            placeholder="z. B. Ist die Marktpositionierung glasklar und differenziert gegenüber Wettbewerbern?" 
                            class="w-full px-3.5 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Bewertungskriterien &amp; Leitfaden (Noten 1 bis 5)</label>
                        <input 
                            type="text" 
                            name="audit_guidance" 
                            placeholder="z. B. 1=Keine Differenzierung, 3=Teilweise Differenzierung, 5=Absolute Preissetzungsmacht &amp; Bekanntheit" 
                            class="w-full px-3 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-slate-300 text-xs focus:border-blue-500 focus:outline-none"
                        >
                    </div>
                </div>

                <!-- Baustein 5: KPI-System -->
                <div class="p-4 rounded-xl bg-dark-card/70 border border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-300 uppercase">
                            5. KPI-System („Keine Frage ohne Messregel“)
                        </label>
                        <span class="text-[10px] font-mono font-bold text-emerald-400 uppercase">Metrik &amp; Trend</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-1">
                            <label class="block text-[11px] text-slate-400 mb-1">Kennzahl-Name</label>
                            <input 
                                type="text" 
                                name="kpi_name" 
                                placeholder="z. B. Preissetzungsmacht-Index" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Zielwert (Soll)</label>
                            <input 
                                type="number" 
                                step="0.1" 
                                name="kpi_target" 
                                placeholder="85.0" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none font-mono"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Einheit</label>
                            <input 
                                type="text" 
                                name="kpi_unit" 
                                placeholder="Index, %, EUR" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none font-mono"
                            >
                        </div>
                    </div>
                </div>

                <!-- Baustein 6: Werkzeuge (Tools) -->
                <div class="p-4 rounded-xl bg-dark-card/70 border border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-300 uppercase">
                            6. Werkzeuge (Tools für operative Befähigung)
                        </label>
                        <span class="text-[10px] font-mono font-bold text-purple-400 uppercase">Lösungsbibliothek</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] text-slate-400 mb-1">Werkzeug- / Vorlagenname</label>
                            <input 
                                type="text" 
                                name="tool_name" 
                                placeholder="z. B. Positionierungs-Playbook &amp; Brand Guidelines SOP" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                            >
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Werkzeug-Typ</label>
                            <select 
                                name="tool_type" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                            >
                                <option value="template">Template / Vorlage</option>
                                <option value="checklist">Checkliste</option>
                                <option value="whitepaper">SOP / Leitfaden</option>
                                <option value="saas">SaaS / Software</option>
                                <option value="ai_function">KI-Workflow</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Baustein 7: Learnings (ALF) -->
                <div class="p-4 rounded-xl bg-dark-card/70 border border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-300 uppercase">
                            7. Learnings (ALF — Empirische Rückkopplung)
                        </label>
                        <span class="text-[10px] font-mono font-bold text-amber-400 uppercase">Kognitiver Pfad</span>
                    </div>
                    <div>
                        <label class="block text-[11px] text-slate-400 mb-1">Ausgangsbeobachtung / Problemstellung</label>
                        <input 
                            type="text" 
                            name="learning_hypothesis" 
                            placeholder="z. B. Uneinheitliche Außendarstellung führte zu anhaltendem Preisdruck bei Neukunden." 
                            class="w-full px-3.5 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                        >
                    </div>
                </div>

                <!-- Baustein 8: Review-Zyklus (ARF) -->
                <div class="p-4 rounded-xl bg-dark-card/70 border border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-300 uppercase">
                            8. Review-Zyklus (ARF — Regelmäßige Überprüfung)
                        </label>
                        <span class="text-[10px] font-mono font-bold text-rose-400 uppercase">Regelkreis</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-400 mb-1">Review-Rhythmus</label>
                            <select 
                                name="review_cadence" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                            >
                                <option value="Q-Review" selected>Quartalsweise (Q-Review)</option>
                                <option value="H-Review">Halbjährlich (H-Review)</option>
                                <option value="J-Review">Jährlich (Strategie-Audit)</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] text-slate-400 mb-1">Erster Review-Fokus / Agenda</label>
                            <input 
                                type="text" 
                                name="review_focus" 
                                placeholder="z. B. Erste Validierung der Positionierungs-Wahrnehmung bei Top-20 Kunden" 
                                class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- Baustein 9: Versionierung & Hierarchie -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            9. Initialer Reifegrad / Version (AMF 1.1)
                        </label>
                        <select 
                            name="amf_version" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none font-mono text-xs"
                        >
                            <option value="v0.1 Konzept" selected>v0.1 Konzept (Initiale Definition &amp; Hypothesen)</option>
                            <option value="v0.2 Erstes Audit">v0.2 Erstes Audit (Fragebogen &amp; Pilot-Auditierung)</option>
                            <option value="v1.0 Produktiv">v1.0 Produktiv (Vollständige KPIs, SOPs &amp; Reviews)</option>
                            <option value="v2.0 Skaliert">v2.0 Skaliert (Gruppenweit ausgerollt &amp; benchmarked)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Hierarchie (Adjazenz-Baum)
                        </label>
                        <select 
                            name="parent_id" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">-- Eigenständige Hauptsäule (Root) --</option>
                            @foreach ($modules as $pMod)
                                <option value="{{ $pMod->id }}">Untergeordnet zu: {{ $pMod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Strategische Beschreibung -->
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                        Strategische Ausrichtung &amp; Handlungsbedarf <span class="text-rose-400">*</span>
                    </label>
                    <textarea 
                        name="description" 
                        rows="2" 
                        required 
                        placeholder="Welches Problem aus Beobachtung/Learning soll durch dieses Modul dauerhaft gelöst werden?" 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                    ></textarea>
                </div>

                <!-- Principle AP-005 Callout in Modal -->
                <div class="p-3.5 rounded-xl bg-blue-950/30 border border-blue-500/30 text-xs text-slate-300 space-y-1">
                    <div class="flex items-center space-x-2 text-blue-400 font-bold">
                        <span>💡</span>
                        <span>Prinzip AP-005: Ein Modul ist kein Audit</span>
                    </div>
                    <p class="text-[11px] text-slate-300 leading-relaxed">
                        Nach dem Anlegen ist das Modul im Dashboard sofort aktiv. Das AMF ermöglicht es Ihnen, für diese Säule gezielt <strong class="text-white">Audit-Fragen</strong>, <strong class="text-white">KPIs („Keine Frage ohne Messregel“)</strong>, <strong class="text-white">Werkzeuge</strong> und <strong class="text-white">Review-Zyklen (ARF)</strong> zu verankern.
                    </p>
                </div>

                <div class="pt-3 border-t border-dark-border flex justify-end space-x-3">
                    <button 
                        type="button" 
                        onclick="closeModal('moduleCreateModal')" 
                        class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition"
                    >
                        Abbrechen
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-md shadow-blue-600/20"
                    >
                        ✓ Modul nach AMF 1.1 anlegen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 9. USER PROFILE & PASSWORD MODAL -->
    <div id="profileModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <!-- Modal Header -->
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 font-bold flex items-center justify-center text-lg font-mono">
                        👤
                    </div>
                    <div>
                        <span class="text-xs font-mono font-bold text-blue-400 uppercase">Kontoeinstellungen</span>
                        <h2 class="text-xl font-bold text-white mt-0.5">Benutzerprofil &amp; Passwort</h2>
                        <p class="text-xs text-slate-300">Verwalten Sie Ihre persönlichen Stammdaten und Ihr Anmeldepasswort.</p>
                    </div>
                </div>
                <button onclick="closeModal('profileModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <!-- Modal Navigation Tabs -->
            <div class="flex border-b border-dark-border bg-[#0d1322] px-6 pt-3 space-x-4">
                <button 
                    type="button"
                    onclick="switchProfileTab('info')" 
                    id="tabBtnProfileInfo"
                    class="pb-2.5 text-xs font-bold border-b-2 border-blue-500 text-blue-400 transition"
                >
                    👤 Profil-Informationen
                </button>
                <button 
                    type="button"
                    onclick="switchProfileTab('password')" 
                    id="tabBtnProfilePassword"
                    class="pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition"
                >
                    🔒 Passwort ändern
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <!-- TAB 1: Profile Information -->
                <div id="profileTabInfo" class="space-y-4">
                    <form method="POST" action="{{ route('actions.profile.update') }}" class="space-y-4 text-sm">
                        @csrf
                        
                        <!-- Account Details Card -->
                        <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border flex items-center justify-between text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Aktuelle Organisation</span>
                                <strong class="text-white font-semibold">{{ $tenant->name ?? 'Disavo Holding GmbH' }}</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 block text-[11px]">Rolle</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                    {{ ucfirst(auth()->user()->tenants()->where('tenants.id', $tenant->id ?? 0)->first()?->pivot?->role ?? 'Owner / Steward') }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Vollständiger Name <span class="text-rose-400">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                required 
                                value="{{ auth()->user()->name }}"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                E-Mail-Adresse <span class="text-rose-400">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                required 
                                value="{{ auth()->user()->email }}"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none"
                            >
                        </div>

                        <div class="pt-3 border-t border-dark-border flex justify-end space-x-3">
                            <button 
                                type="button" 
                                onclick="closeModal('profileModal')" 
                                class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition"
                            >
                                Schließen
                            </button>
                            <button 
                                type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-md shadow-blue-600/20"
                            >
                                ✓ Profil speichern
                            </button>
                        </div>
                    </form>
                </div>

                <!-- TAB 2: Change Password -->
                <div id="profileTabPassword" class="space-y-4 hidden">
                    <form method="POST" action="{{ route('actions.profile.password') }}" class="space-y-4 text-sm">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Aktuelles Passwort <span class="text-rose-400">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="current_password" 
                                required 
                                placeholder="••••••••"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none font-mono"
                            >
                            <span class="text-[11px] text-slate-400 mt-0.5 block">Geben Sie zur Bestätigung Ihr bisheriges Passwort ein.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Neues Passwort <span class="text-rose-400">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                required 
                                minlength="8"
                                placeholder="Mindestens 8 Zeichen"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none font-mono"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Neues Passwort bestätigen <span class="text-rose-400">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                minlength="8"
                                placeholder="Passwort wiederholen"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none font-mono"
                            >
                        </div>

                        <div class="p-3.5 rounded-xl bg-amber-950/30 border border-amber-500/30 text-xs text-amber-200">
                            <strong>Sicherheitshinweis:</strong> Nach der Änderung bleibt Ihre aktuelle Sitzung aktiv. Verwenden Sie beim nächsten Login Ihr neues Passwort.
                        </div>

                        <div class="pt-3 border-t border-dark-border flex justify-end space-x-3">
                            <button 
                                type="button" 
                                onclick="closeModal('profileModal')" 
                                class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition"
                            >
                                Abbrechen
                            </button>
                            <button 
                                type="submit" 
                                class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-xs font-bold text-white transition shadow-md shadow-purple-600/20"
                            >
                                🔒 Neues Passwort festlegen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 10. AUDIT DESIGNER MODAL (ARF Custom Audit Builder) -->
    <div id="auditDesignerModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase">ARF Audit-Designer</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-blue-400 font-semibold">Spezial-Audit entwerfen</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mt-1">🎯 Spezial-Audit entwerfen</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Erstellen Sie ein maßgeschneidertes Prüfverfahren mit individuellen Fragen, Gewichtungen und Reifegrad-Skalen.
                    </p>
                </div>
                <button onclick="closeModal('auditDesignerModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.audit-template.create') }}" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Zugeordnetes Modul <span class="text-slate-500 font-normal">(Optional für modulübergreifende Audits)</span>
                        </label>
                        <select name="module_id" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none">
                            <option value="">(Kein Modul / Übergreifendes Holding-Audit)</option>
                            @foreach ($modules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Name des Audits <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="z.B. Marken-Konsistenz & Positionierungs-Audit" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Beschreibung &amp; Prüfziel
                        </label>
                        <textarea name="description" rows="2" placeholder="Zweck des Audits und Zielzustand, der bewertet wird..." class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                    </div>

                    <!-- Dynamic Questions Container -->
                    <div class="pt-2 border-t border-dark-border/80">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h4 class="font-mono text-emerald-400 font-bold uppercase text-[11px]">Audit-Fragen &amp; Kriterien (1-5 Skala)</h4>
                                <p class="text-slate-400 text-[11px]">Fügen Sie spezifische Fragestellungen mit Gewichtung hinzu:</p>
                            </div>
                            <button type="button" onclick="addCustomAuditQuestionRow()" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] transition flex items-center space-x-1">
                                <span>+</span>
                                <span>Frage hinzufügen</span>
                            </button>
                        </div>

                        <div id="customAuditQuestionsContainer" class="space-y-3">
                            <div class="custom-question-row p-3.5 rounded-xl bg-dark-card border border-dark-border space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-mono text-emerald-400 font-semibold row-number">Frage 01</span>
                                    <button type="button" onclick="removeCustomAuditQuestionRow(this)" class="text-xs text-slate-500 hover:text-rose-400 transition" title="Frage entfernen">✕</button>
                                </div>
                                <div>
                                    <input type="text" name="questions[0][question_text]" required placeholder="Konkrete Audit-Frage eingeben..." class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-[11px] text-slate-400 block mb-0.5">Gewichtung (1 bis 5):</label>
                                        <select name="questions[0][weight]" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                                            <option value="1">1 (Niedrig)</option>
                                            <option value="2">2 (Mittel)</option>
                                            <option value="3" selected>3 (Standard)</option>
                                            <option value="4">4 (Hoch)</option>
                                            <option value="5">5 (Kritisch)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[11px] text-slate-400 block mb-0.5">Bewertungshinweis (1 = Min, 5 = Max):</label>
                                        <input type="text" name="questions[0][guidance]" placeholder="z.B. 1=keine Dokumentation, 5=vollständig institutionalisiert" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('auditDesignerModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Abbrechen</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-md shadow-blue-600/20">
                        ✓ Spezial-Audit speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 11. AUDIT SCHEDULER MODAL (ARF Schedule & Assignment) -->
    <div id="auditScheduleModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase">ARF Audit-Scheduler</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-emerald-400 font-semibold">Terminieren &amp; Zuweisen</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mt-1">📅 Audit ansetzen &amp; zuweisen</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Legen Sie Verantwortlichkeiten, Rhythmus und Fristen für kontinuierliche Überprüfungen fest.
                    </p>
                </div>
                <button onclick="closeModal('auditScheduleModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.audit-run.schedule') }}" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Modul auswählen <span class="text-rose-400">*</span>
                        </label>
                        <select name="module_id" id="scheduleModalModuleSelect" required onchange="onScheduleModuleChanged(this.value)" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-emerald-500 focus:outline-none">
                            @foreach ($modules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Audit-Vorlage (Template) <span class="text-rose-400">*</span>
                        </label>
                        <select name="audit_template_id" id="scheduleModalTemplateSelect" required class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-emerald-500 focus:outline-none">
                            @foreach ($auditTemplates as $tpl)
                                <option value="{{ $tpl->id }}" data-module-id="{{ $tpl->module_id }}">
                                    {{ $tpl->name }} {{ $tpl->module ? '(' . $tpl->module->name . ')' : '(Übergreifend)' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Prüfer / Auditor <span class="text-rose-400">*</span>
                            </label>
                            <select name="auditor_id" required class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}" {{ $u->id === auth()->id() ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Rhythmus (Kadenz)
                            </label>
                            <select name="cadence" class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                                <option value="one_time">Einmalig</option>
                                <option value="monthly">Monatlich</option>
                                <option value="quarterly" selected>Quartalsweise (Standard)</option>
                                <option value="semi_annual">Halbjährlich</option>
                                <option value="annual">Jährlich</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Fälligkeitsdatum
                            </label>
                            <input type="date" name="due_date" value="{{ now()->addDays(14)->format('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Titel / Bezeichnung
                            </label>
                            <input type="text" name="title" placeholder="z.B. Q4 Reifegrad-Prüfung" class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Prüfhinweise &amp; Schwerpunkte
                        </label>
                        <textarea name="notes" rows="2" placeholder="Besondere Fokus-Themen oder Prüfungsvorgaben für den Auditor..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('auditScheduleModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Abbrechen</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white transition shadow-md shadow-emerald-600/20">
                        ✓ Audit ansetzen &amp; zuweisen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 12. TOOL DESIGNER MODAL (AMF Tool-Studio) -->
    <div id="toolDesignerModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">AMF Tool-Studio</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-amber-400 font-semibold">Operatives Werkzeug</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mt-1">🛠️ Werkzeug entwerfen</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Erstellen Sie operative Checklisten, Standard Operating Procedures (SOPs), Vorlagen oder KI-Prompts für Ihre Module.
                    </p>
                </div>
                <button onclick="closeModal('toolDesignerModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.tool.create') }}" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Zielmodul auswählen <span class="text-rose-400">*</span>
                        </label>
                        <select name="module_id" id="toolDesignerModuleSelect" required class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none">
                            @foreach ($modules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Name des Werkzeugs <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" name="name" required placeholder="z.B. Brand Launch Checkliste" class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                                Typ des Werkzeugs <span class="text-rose-400">*</span>
                            </label>
                            <select name="type" id="toolTypeSelect" onchange="onToolTypeChanged(this.value)" class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                                <option value="checklist">Interaktive Checkliste</option>
                                <option value="sop">SOP / Leitfaden (Markdown)</option>
                                <option value="template">Dokument- / Vertragsvorlage</option>
                                <option value="prompt">KI-Prompt (System-Prompt)</option>
                                <option value="saas">SaaS / Web-Tool Link</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Kurzbeschreibung &amp; Anwendungszweck
                        </label>
                        <textarea name="description" rows="2" placeholder="Wann und wie wird dieses Werkzeug operativ angewendet?" class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-300 uppercase" id="toolContentLabel">
                                Inhalt (Punkte zeilenweise eingeben)
                            </label>
                            <span class="text-[11px] text-slate-400" id="toolContentHint">1 Zeile = 1 Checklistenpunkt</span>
                        </div>
                        <textarea name="content" id="toolContentInput" rows="6" placeholder="1. Erste Anforderung prüfen&#10;2. Dokumentation ablegen&#10;3. Freigabe durch Geschäftsleitung einholen" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none font-mono leading-relaxed"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('toolDesignerModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Abbrechen</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-xs font-bold text-white transition shadow-md shadow-amber-600/20">
                        ✓ Werkzeug aktivieren
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 13. TOOL VIEWER MODAL (Interactive Tool Runner) -->
    <div id="toolViewerModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-2xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span id="tvBadgeType" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">Checkliste</span>
                        <span class="text-slate-500">•</span>
                        <span id="tvBadgeModule" class="text-xs font-mono text-blue-400 font-semibold">Markenaufbau</span>
                    </div>
                    <h2 id="tvTitle" class="text-xl font-bold text-white mt-1">Werkzeug</h2>
                    <p id="tvDescription" class="text-xs text-slate-300 mt-1"></p>
                </div>
                <button onclick="closeModal('toolViewerModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                <!-- Progress bar for checklists -->
                <div id="tvProgressContainer" class="p-3.5 rounded-xl bg-dark-card border border-dark-border hidden">
                    <div class="flex items-center justify-between text-xs font-mono mb-1.5">
                        <span class="text-slate-300 font-semibold">Erledigungsgrad:</span>
                        <span id="tvProgressText" class="text-emerald-400 font-bold">0% (0/0)</span>
                    </div>
                    <div class="w-full bg-dark-surface h-2 rounded-full overflow-hidden border border-dark-border/60">
                        <div id="tvProgressBar" class="bg-emerald-500 h-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div id="tvContent" class="space-y-3"></div>
            </div>

            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-between items-center">
                <div id="tvActionButtons"></div>
                <button onclick="closeModal('toolViewerModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition">Schließen</button>
            </div>
        </div>
    </div>

    <!-- 14. PROMOTE LEARNING TO PRINCIPLE MODAL (ALF) -->
    <div id="learningPromoteModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30 uppercase">ALF Stufe 4</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-purple-400 font-semibold">Prinzipienschmiede</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mt-1">👑 Erkenntnis zum Prinzip erheben</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Formulieren Sie eine unverrückbare Handlungsregel und weisen Sie das gesteuerte Modul zu.
                    </p>
                </div>
                <button onclick="closeModal('learningPromoteModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.alf.promote') }}" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <input type="hidden" name="learning_id" id="promoteFormLearningId" value="">

                <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Name des Prinzips <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="title" id="promoteFormTitle" required class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-purple-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Verbindlicher Grundsatz (Statement) <span class="text-rose-400">*</span>
                        </label>
                        <textarea name="statement" id="promoteFormStatement" required rows="3" placeholder="Der Grundsatz in einem prägnanten Satz formuliert..." class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-purple-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Begründung &amp; Entstehungsursache (Rationale)
                        </label>
                        <textarea name="rationale" rows="2" placeholder="Warum gilt dieses Prinzip? Welche Praxiserfahrung liegt zugrunde?" class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-purple-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Gesteuertes Modul (Governs-Relation) <span class="text-slate-500 font-normal">(Optional)</span>
                        </label>
                        <select name="target_module_id" class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-purple-500 focus:outline-none">
                            <option value="">(Kein Modul / Übergreifendes Prinzip)</option>
                            @foreach ($modules as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-[11px] text-slate-400 mt-1 block">Verknüpft die semantische Kante <code class="text-purple-300 font-mono">Principle -(governs)-&gt; Module</code> im Wissensgraphen.</span>
                    </div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('learningPromoteModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Abbrechen</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-xs font-bold text-white transition shadow-md shadow-purple-600/20">
                        👑 Als Prinzip ratifizieren
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 15. SYNTHESIZE OBSERVATION TO LEARNING MODAL (ALF) -->
    <div id="learningSynthesizeModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-lg w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">ALF Stufe 2</span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-amber-400 font-semibold">Erkenntnisgewinnung</span>
                    </div>
                    <h2 class="text-xl font-bold text-white mt-1">💡 Zu Erkenntnis verdichten</h2>
                    <p class="text-xs text-slate-300 mt-1">
                        Verdichten Sie eine rohe Beobachtung zu einem systematischen Learning-Draft.
                    </p>
                </div>
                <button onclick="closeModal('learningSynthesizeModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.alf.synthesize') }}" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <input type="hidden" name="observation_id" id="synthFormObsId" value="">

                <div class="p-6 overflow-y-auto space-y-4 flex-1 text-xs">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Titel der Erkenntnis <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" name="title" id="synthFormTitle" required class="w-full px-3.5 py-2.5 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Zusammenfassung der Erkenntnis (Summary) <span class="text-rose-400">*</span>
                        </label>
                        <textarea name="summary" id="synthFormSummary" required rows="3" placeholder="Was haben wir gelernt? Welches Muster wurde erkannt?" class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">
                            Kontext &amp; Begründung (Rationale)
                        </label>
                        <textarea name="rationale" rows="2" placeholder="Ausgangslage und Ursachenanalyse..." class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none"></textarea>
                    </div>
                </div>

                <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('learningSynthesizeModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Abbrechen</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-xs font-bold text-white transition shadow-md shadow-amber-600/20">
                        💡 Als Entwurf erfassen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 16. ALLOCORE MASTER AUDIT (AMAR) AUDIT CONDUCTOR MODAL -->
    @php
        $amarTemplate = $auditTemplates->first(fn ($t) => str_contains(strtolower($t->name), 'amar'));
        $amarDefinitions = \App\Domains\Development\Services\AmarTemplateService::getDefinition();
        $amarQuestionsByArea = $amarTemplate ? $amarTemplate->questions->groupBy('area') : collect();
        $latestCompletedAmar = $auditRuns->first(fn ($r) => $r->isAmar() && $r->isCompleted());
        $previousAmarScore = $latestCompletedAmar ? round((float) $latestCompletedAmar->score) : null;
    @endphp
    <div id="amarAuditModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-5xl w-full max-h-[95vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <!-- Modal Header -->
            <div class="p-5 sm:p-6 border-b border-dark-border flex items-start justify-between bg-dark-card shrink-0">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/40 uppercase">
                            👑 Kanonisches Master-Audit
                        </span>
                        <span class="text-slate-500">•</span>
                        <span class="text-xs font-mono text-emerald-400 font-semibold">Allocore Review Framework (ARF)</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white mt-1">ALLOCORE MASTER AUDIT (AMAR 1.0)</h2>
                    <p class="text-xs text-slate-300 mt-1 max-w-3xl">
                        Standardisiertes Qualitätsaudit über alle 10 Allocore-Bereiche inklusive Unternehmer-Test, Fehlerprotokollierung und kybernetischem Abschluss-Tor.
                    </p>
                </div>
                <button onclick="closeModal('amarAuditModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <!-- Modal Sub-Navigation Bar -->
            <div class="flex border-b border-dark-border bg-[#0d1322] px-6 pt-3 space-x-4 shrink-0 overflow-x-auto">
                <button 
                    type="button" 
                    onclick="switchAmarTab('areas')" 
                    id="amarTabBtnAreas" 
                    class="pb-2.5 text-xs font-bold border-b-2 border-blue-500 text-blue-400 transition whitespace-nowrap flex items-center space-x-1.5"
                >
                    <span>📋</span>
                    <span>10 Audit-Bereiche (Fragen)</span>
                </button>
                <button 
                    type="button" 
                    onclick="switchAmarTab('defects')" 
                    id="amarTabBtnDefects" 
                    class="pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition whitespace-nowrap flex items-center space-x-1.5"
                >
                    <span>🚨</span>
                    <span>Insight-Protokoll (<span id="amarDefectCountBadge">0</span>)</span>
                </button>
                <button 
                    type="button" 
                    onclick="switchAmarTab('cybernetics')" 
                    id="amarTabBtnCybernetics" 
                    class="pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition whitespace-nowrap flex items-center space-x-1.5"
                >
                    <span>🔄</span>
                    <span>Kybernetik &amp; Freigabe-Tor</span>
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('actions.audit.submit') }}" id="amarForm" class="flex flex-col flex-grow overflow-hidden">
                @csrf
                <input type="hidden" name="module_id" value="{{ $amarTemplate ? $amarTemplate->module_id : ($modules->first()?->id ?? 1) }}">
                <input type="hidden" name="audit_template_id" value="{{ $amarTemplate ? $amarTemplate->id : 1 }}">
                <input type="hidden" name="audit_run_id" id="amarFormRunId" value="">

                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-6 text-xs">
                    <!-- AUDIT INFORMATION HEADER CARD -->
                    <div class="p-4 rounded-xl bg-dark-card border border-dark-border grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                        <div>
                            <span class="text-slate-400 text-[11px] block">Audit-Datum:</span>
                            <span class="text-white font-mono font-semibold">{{ now()->format('d.m.Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px] block">Auditor:</span>
                            <span class="text-purple-300 font-semibold truncate block">{{ auth()->user()->name }}</span>
                        </div>
                        <div>
                            <label class="text-slate-400 text-[11px] block">Version:</label>
                            <input type="text" name="version" value="1.0" class="w-full px-2 py-1 rounded bg-dark-surface border border-dark-border text-white text-xs font-mono focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-slate-400 text-[11px] block">Geprüfte Nutzerpfade:</label>
                            <input type="number" name="user_paths_audited" value="1" min="1" class="w-full px-2 py-1 rounded bg-dark-surface border border-dark-border text-white text-xs font-mono focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-slate-400 text-[11px] block">Geprüfte Tools:</label>
                            <input type="number" name="tools_audited" value="{{ $modules->sum(fn($m) => $m->tools->count()) ?: 5 }}" min="0" class="w-full px-2 py-1 rounded bg-dark-surface border border-dark-border text-white text-xs font-mono focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="text-slate-400 text-[11px] block">Vorheriger Score (%):</label>
                            <input type="number" name="previous_score" value="{{ $previousAmarScore ?? '' }}" placeholder="z.B. 75" min="0" max="100" class="w-full px-2 py-1 rounded bg-dark-surface border border-dark-border text-white text-xs font-mono focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>

                    <!-- MANDATORY ENTREPRENEUR TEST BANNER -->
                    <div class="p-4 rounded-xl bg-purple-950/30 border border-purple-500/40 space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-base">👑</span>
                            <h4 class="text-sm font-bold text-purple-300 uppercase tracking-wide">Der Unternehmer-Test (Mandatorisch)</h4>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Der Auditor absolviert einen <strong>vollständigen Nutzerpfad</strong>:
                            <span class="font-mono text-purple-300">Registrierung &rarr; Audit &rarr; Ergebnis &rarr; Coach &rarr; Buch &rarr; Tool &rarr; Fortschritt</span>.
                        </p>
                        <p class="text-xs text-slate-200 font-semibold italic">
                            „Würde ein Unternehmer nach 90 Minuten in Allocore sagen: ‚Das hat meinem Unternehmen wirklich geholfen.‘?“
                        </p>
                        <div class="pt-1">
                            <label class="flex items-center space-x-2.5 cursor-pointer select-none">
                                <input type="checkbox" name="entrepreneur_test_passed" value="1" required class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500 bg-dark-surface border-dark-border">
                                <span class="text-xs font-bold text-white">
                                    Ich bestätige, dass ich den vollen 90-Minuten-Nutzerpfad absolviert habe und dieser Test bestanden ist.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- TAB 1: 10 AUDIT AREAS -->
                    <div id="amarSectionAreas" class="space-y-6">
                        @if ($amarTemplate && $amarTemplate->questions->isNotEmpty())
                            @foreach ($amarDefinitions as $areaName => $areaDef)
                                @php
                                    $questionsInArea = $amarQuestionsByArea->get($areaName, collect());
                                @endphp
                                <div class="rounded-xl border border-dark-border bg-dark-card/60 overflow-hidden">
                                    <!-- Area Header -->
                                    <div class="p-4 bg-dark-card border-b border-dark-border flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 uppercase">
                                                    {{ $areaName }}
                                                </span>
                                                <span class="text-slate-400 font-mono text-[11px]">({{ $questionsInArea->count() }} Fragen)</span>
                                            </div>
                                            <p class="text-xs text-slate-300 mt-1 font-medium">
                                                🎯 <strong>Ziel:</strong> {{ $areaDef['goal'] }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Questions Container -->
                                    <div class="p-4 space-y-4">
                                        @php
                                            $currentSection = null;
                                        @endphp
                                        @foreach ($questionsInArea as $q)
                                            @if ($q->section_header && $q->section_header !== $currentSection)
                                                @php $currentSection = $q->section_header; @endphp
                                                <div class="pt-2 pb-1 border-b border-dark-border/60">
                                                    <span class="text-[11px] font-mono font-bold text-amber-400 uppercase tracking-wider">
                                                        ▸ {{ $currentSection }}
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="p-3.5 rounded-xl bg-dark-surface border border-dark-border space-y-2.5">
                                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2">
                                                    <div class="space-y-0.5 flex-1">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs font-bold text-white">{{ $q->question_text }}</span>
                                                            @if ($q->isBinary())
                                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                                    Schlüsseltest
                                                                </span>
                                                            @else
                                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-semibold bg-slate-800 text-slate-400">
                                                                    Gewicht: {{ $q->weight }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if ($q->guidance)
                                                            <span class="text-[11px] text-slate-400 block">{{ $q->guidance }}</span>
                                                        @endif
                                                    </div>

                                                    <!-- Usability Status Symbol Selector -->
                                                    <div class="shrink-0 flex items-center space-x-1.5">
                                                        <label class="cursor-pointer">
                                                            <input type="radio" name="responses[{{ $q->id }}][status_symbol]" value="usable" checked class="sr-only peer">
                                                            <span class="px-2 py-1 rounded text-xs border border-dark-border text-slate-400 peer-checked:bg-emerald-500/20 peer-checked:text-emerald-300 peer-checked:border-emerald-500/40 transition select-none inline-flex items-center space-x-1" title="Sofort nutzbar">
                                                                <span>✅</span>
                                                                <span class="text-[10px] font-semibold hidden sm:inline">Nutzbar</span>
                                                            </span>
                                                        </label>
                                                        <label class="cursor-pointer">
                                                            <input type="radio" name="responses[{{ $q->id }}][status_symbol]" value="improvement" class="sr-only peer">
                                                            <span class="px-2 py-1 rounded text-xs border border-dark-border text-slate-400 peer-checked:bg-amber-500/20 peer-checked:text-amber-300 peer-checked:border-amber-500/40 transition select-none inline-flex items-center space-x-1" title="Verbesserungsbedarf">
                                                                <span>⚠️</span>
                                                                <span class="text-[10px] font-semibold hidden sm:inline">Bedarf</span>
                                                            </span>
                                                        </label>
                                                        <label class="cursor-pointer">
                                                            <input type="radio" name="responses[{{ $q->id }}][status_symbol]" value="defect" class="sr-only peer">
                                                            <span class="px-2 py-1 rounded text-xs border border-dark-border text-slate-400 peer-checked:bg-rose-500/20 peer-checked:text-rose-300 peer-checked:border-rose-500/40 transition select-none inline-flex items-center space-x-1" title="Kritischer Mangel">
                                                                <span>❌</span>
                                                                <span class="text-[10px] font-semibold hidden sm:inline">Mangel</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Score Input (Binary or 0-5 scale) -->
                                                <div class="pt-2 border-t border-dark-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                                    @if ($q->isBinary())
                                                        <div class="flex items-center space-x-4">
                                                            <span class="text-xs text-slate-300 font-semibold">Testergebnis:</span>
                                                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                                                <input type="radio" name="responses[{{ $q->id }}][binary_answer]" value="yes" checked class="w-3.5 h-3.5 text-emerald-500 focus:ring-emerald-500 bg-dark-card border-dark-border">
                                                                <span class="text-xs font-bold text-emerald-400">JA (Erfüllt)</span>
                                                            </label>
                                                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                                                <input type="radio" name="responses[{{ $q->id }}][binary_answer]" value="no" class="w-3.5 h-3.5 text-rose-500 focus:ring-rose-500 bg-dark-card border-dark-border">
                                                                <span class="text-xs font-bold text-rose-400">NEIN (Nicht erfüllt)</span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-[11px] text-slate-400">Bewertung:</span>
                                                            <div class="flex items-center space-x-1">
                                                                @foreach ([
                                                                    0 => '0 - Nicht vorhanden',
                                                                    1 => '1 - Schwer mangelhaft',
                                                                    2 => '2 - Schwach',
                                                                    3 => '3 - Akzeptabel',
                                                                    4 => '4 - Gut',
                                                                    5 => '5 - Exzellent',
                                                                ] as $val => $label)
                                                                    <label class="cursor-pointer">
                                                                        <input type="radio" name="responses[{{ $q->id }}][score]" value="{{ $val }}" {{ $val === 4 ? 'checked' : '' }} class="sr-only peer">
                                                                        <span class="w-7 h-7 rounded-lg border border-dark-border flex items-center justify-center font-mono font-bold text-xs text-slate-400 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-500 transition select-none hover:bg-dark-hover" title="{{ $label }}">
                                                                            {{ $val }}
                                                                        </span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="flex-1 max-w-sm">
                                                        <input type="text" name="responses[{{ $q->id }}][notes]" placeholder="Optionale Anmerkung / Beobachtung..." class="w-full px-2.5 py-1.5 rounded-lg bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-8 text-center bg-dark-card rounded-xl border border-dark-border space-y-3">
                                <span class="text-3xl">⚠️</span>
                                <h4 class="text-white font-bold">Kanonisches AMAR-Template wird initialisiert...</h4>
                                <p class="text-xs text-slate-400">Klicken Sie auf den Button unten, um das AMAR Master-Audit Template mit allen 10 Bereichen anzulegen.</p>
                                <form method="POST" action="{{ route('actions.audit-template.ensure-amar') }}">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition">
                                        ⚡ AMAR-Template jetzt erstellen
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <!-- TAB 2: INSIGHT PROTOCOL (DEFECT LOGGING) -->
                    <div id="amarSectionDefects" class="space-y-4 hidden">
                        <div class="p-4 rounded-xl bg-dark-card border border-dark-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-bold text-white flex items-center space-x-2">
                                    <span>🚨</span>
                                    <span>Erkenntnis- &amp; Mängelprotokoll (Insight Protocol)</span>
                                </h4>
                                <p class="text-xs text-slate-300 mt-0.5">
                                    Strukturierte Erfassung aller bei der Prüfung entdeckten Engpässe, Reibungen und Mängel inklusive Verantwortlichkeit und Hebelmaßnahme.
                                </p>
                            </div>
                            <button type="button" onclick="addAmarDefectRow()" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs transition flex items-center space-x-1 shrink-0 shadow-sm">
                                <span>+</span>
                                <span>Mangel erfassen</span>
                            </button>
                        </div>

                        <div id="amarDefectsContainer" class="space-y-4">
                            <!-- Dynamic defect rows inserted here -->
                        </div>

                        <div id="amarDefectsEmptyState" class="p-8 text-center rounded-xl bg-dark-card/50 border border-dashed border-dark-border text-slate-400 space-y-2">
                            <span class="text-2xl block">✓</span>
                            <p class="font-semibold text-slate-300">Bisher keine spezifischen Mängel protokolliert.</p>
                            <p class="text-[11px]">Klicken Sie auf <strong>„Mangel erfassen“</strong>, um Schwachstellen mit Schweregrad, Auswirkung und Lösungsmaßnahme zu dokumentieren.</p>
                        </div>
                    </div>

                    <!-- TAB 3: CYBERNETIC FEEDBACK & COMPLETION GATE -->
                    <div id="amarSectionCybernetics" class="space-y-6 hidden">
                        <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-1">
                            <h4 class="text-sm font-bold text-white flex items-center space-x-2">
                                <span>🔄</span>
                                <span>Kybernetische Feedback-Schleife (Cybernetic Reflection)</span>
                            </h4>
                            <p class="text-xs text-slate-300">
                                Beantworten Sie die 6 Reflexionsfragen, um systemische Erkenntnisse zu sichern und den Regelkreis zu schließen.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-300">
                                    1. Welcher Bereich hat die höchste Hebelwirkung auf den Unternehmerwert?
                                </label>
                                <textarea name="cybernetic_feedback[highest_leverage]" rows="2" placeholder="Konkreter Bereich und Begründung..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-300">
                                    2. Welche Tools müssen vereinfacht oder entfernt werden?
                                </label>
                                <textarea name="cybernetic_feedback[tools_to_simplify]" rows="2" placeholder="Komplexe Werkzeuge oder Überflüssiges..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-300">
                                    3. Wo bricht der Nutzerpfad am ehesten ab?
                                </label>
                                <textarea name="cybernetic_feedback[user_path_dropout]" rows="2" placeholder="Größte Hürde oder Reibung im Flow..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-300">
                                    4. Was war die überraschendste Erkenntnis des Audits?
                                </label>
                                <textarea name="cybernetic_feedback[surprising_insight]" rows="2" placeholder="Unerwartete Beobachtung..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-300">
                                    5. Welcher Engpass bremst Allocore aktuell am meisten?
                                </label>
                                <textarea name="cybernetic_feedback[main_bottleneck]" rows="2" placeholder="Limitierender Faktor (Theory of Constraints)..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-300">
                                    6. Welche 3 Maßnahmen haben oberste Priorität bis zum nächsten Audit?
                                </label>
                                <textarea name="cybernetic_feedback[top_priorities]" rows="2" placeholder="1. ...&#10;2. ...&#10;3. ..." class="w-full px-3 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-xs focus:border-blue-500 focus:outline-none"></textarea>
                            </div>
                        </div>

                        <!-- FINAL COMPLETION GATE (NEW FEATURE BAN TRIGGER) -->
                        <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-[#151b2e] to-slate-900 border-2 border-blue-500/50 shadow-xl space-y-4">
                            <div class="flex items-start space-x-3">
                                <span class="text-2xl">⚖️</span>
                                <div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/40">
                                        Abschluss-Tor (Kybernetische Freigabe)
                                    </span>
                                    <h3 class="text-base font-bold text-white mt-1">
                                        Ist Allocore für einen Unternehmer heute wertvoller als vor dem letzten Audit?
                                    </h3>
                                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                                        <strong>Stopp-Regel:</strong> Falls <strong>NEIN</strong>, wird ein systemweiter 
                                        <span class="text-rose-400 font-bold font-mono">NEW FEATURE BAN</span> verhängt.
                                        Es dürfen keine neuen Module oder Funktionen begonnen werden, bis die identifizierten Engpässe behoben sind.
                                    </p>
                                </div>
                            </div>

                            <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                                <label class="flex-1 p-3.5 rounded-xl border border-emerald-500/40 bg-emerald-950/20 hover:bg-emerald-950/40 cursor-pointer transition flex items-center space-x-3">
                                    <input type="radio" name="cybernetic_feedback[more_valuable_today]" value="yes" checked class="w-4 h-4 text-emerald-500 focus:ring-emerald-500 bg-dark-card border-dark-border">
                                    <div>
                                        <span class="text-xs font-extrabold text-emerald-400 block">✓ JA – System ist wertvoller</span>
                                        <span class="text-[11px] text-slate-300">Erkenntnisse &amp; Verbesserungen greifen. Weiterentwicklung freigegeben.</span>
                                    </div>
                                </label>

                                <label class="flex-1 p-3.5 rounded-xl border border-rose-500/40 bg-rose-950/20 hover:bg-rose-950/40 cursor-pointer transition flex items-center space-x-3">
                                    <input type="radio" name="cybernetic_feedback[more_valuable_today]" value="no" class="w-4 h-4 text-rose-500 focus:ring-rose-500 bg-dark-card border-dark-border">
                                    <div>
                                        <span class="text-xs font-extrabold text-rose-400 block">🚨 NEIN – Stop &amp; Fix</span>
                                        <span class="text-[11px] text-slate-300">Löst sofortigen <strong>New Feature Ban</strong> aus! Vorrang für Fehlerbehebung.</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-dark-border bg-dark-card flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-2 text-slate-400 text-xs">
                        <span>💡</span>
                        <span class="hidden sm:inline">Alle 10 Bereiche werden gewichtet zum Allocore Value Score verdichtet.</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="closeModal('amarAuditModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">
                            Abbrechen
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-lg shadow-blue-600/30 flex items-center space-x-1.5">
                            <span>👑</span>
                            <span>Master-Audit einreichen &amp; bewerten</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 17. AMAR DETAIL INSPECTOR MODAL -->
    <div id="amarDetailModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-4xl w-full max-h-[94vh] flex flex-col overflow-hidden shadow-2xl animate-fadeIn">
            <!-- Header -->
            <div class="p-5 sm:p-6 border-b border-dark-border flex items-start justify-between bg-dark-card shrink-0">
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/40 uppercase">
                            👑 AMAR Auswertung
                        </span>
                        <span class="text-slate-500">•</span>
                        <span id="admDate" class="text-xs font-mono text-slate-400"></span>
                    </div>
                    <h2 id="admTitle" class="text-xl sm:text-2xl font-black text-white"></h2>
                    <p id="admSubtitle" class="text-xs text-slate-300"></p>
                </div>
                <button onclick="closeModal('amarDetailModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <!-- Content -->
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-6 text-xs">
                <!-- SCORE & GATES HERO CARD -->
                <div class="p-5 rounded-2xl bg-gradient-to-r from-[#0d162e] via-[#141b38] to-[#0d162e] border border-blue-500/40 grid grid-cols-1 sm:grid-cols-3 gap-4 text-center items-center">
                    <div class="space-y-1">
                        <span class="text-[11px] font-mono uppercase text-slate-400">Allocore Value Score</span>
                        <div class="flex items-center justify-center space-x-1">
                            <span id="admScoreValue" class="text-4xl sm:text-5xl font-black text-white font-mono"></span>
                            <span class="text-xl font-bold text-blue-400">%</span>
                        </div>
                        <div id="admScoreDelta" class="text-[11px] font-mono text-slate-400"></div>
                    </div>

                    <div class="space-y-1.5 sm:border-x sm:border-dark-border sm:px-4">
                        <span class="text-[11px] font-mono uppercase text-slate-400">Unternehmer-Test</span>
                        <div id="admEntrepreneurTestBadge" class="inline-block px-3 py-1.5 rounded-xl font-mono text-xs font-bold"></div>
                        <p class="text-[10px] text-slate-400 leading-tight">90-Minuten-Vollpfad: Reg &rarr; Audit &rarr; Coach &rarr; Tool</p>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-[11px] font-mono uppercase text-slate-400">Kybernetischer Status</span>
                        <div id="admFeatureBanBadge" class="inline-block px-3 py-1.5 rounded-xl font-mono text-xs font-bold"></div>
                        <p id="admFeatureBanHint" class="text-[10px] text-slate-400 leading-tight"></p>
                    </div>
                </div>

                <!-- 10-AREA BREAKDOWN BARS -->
                <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-bold text-white flex items-center space-x-2">
                            <span>📊</span>
                            <span>Ergebnis nach den 10 Allocore-Bereichen</span>
                        </h4>
                        <span class="text-[11px] font-mono text-slate-400">Gleichmäßig gewichtet (0-100%)</span>
                    </div>

                    <div id="admAreaScoresContainer" class="space-y-2.5 pt-1">
                        <!-- Dynamic area bars -->
                    </div>
                </div>

                <!-- INSIGHT PROTOCOLS / DEFECTS -->
                <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-3">
                    <h4 class="text-sm font-bold text-white flex items-center space-x-2">
                        <span>🚨</span>
                        <span>Dokumentierte Mängel &amp; Erkenntnisse (Insight Protocol)</span>
                    </h4>
                    <div id="admDefectsContainer" class="space-y-2.5">
                        <!-- Dynamic defect cards -->
                    </div>
                </div>

                <!-- CYBERNETIC FEEDBACK ANSWERS -->
                <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-3">
                    <h4 class="text-sm font-bold text-white flex items-center space-x-2">
                        <span>🔄</span>
                        <span>Kybernetische Reflexion &amp; Maßnahmen</span>
                    </h4>
                    <div id="admCyberFeedbackContainer" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <!-- Dynamic cyber feedback items -->
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end shrink-0">
                <button type="button" onclick="closeModal('amarDetailModal')" class="px-5 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-bold text-slate-200 transition">
                    Schließen
                </button>
            </div>
        </div>
    </div>

    <!-- CLIENT-SIDE JAVASCRIPT FOR INTERACTIVITY -->
    <script>
        const MODULES_DATA = @json($modules);
        const AUDIT_TEMPLATES_DATA = @json($auditTemplates);
        const ALL_AUDIT_RUNS_DATA = @json($auditRuns);

        function openUserModal() {
            openModal('userModal');
        }

        function openProfileModal(tab = 'info') {
            switchProfileTab(tab);
            openModal('profileModal');
        }

        function switchProfileTab(tab) {
            const tabInfo = document.getElementById('profileTabInfo');
            const tabPass = document.getElementById('profileTabPassword');
            const btnInfo = document.getElementById('tabBtnProfileInfo');
            const btnPass = document.getElementById('tabBtnProfilePassword');

            if (!tabInfo || !tabPass) return;

            if (tab === 'password') {
                tabInfo.classList.add('hidden');
                tabPass.classList.remove('hidden');
                btnInfo.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition';
                btnPass.className = 'pb-2.5 text-xs font-bold border-b-2 border-purple-500 text-purple-400 transition';
            } else {
                tabInfo.classList.remove('hidden');
                tabPass.classList.add('hidden');
                btnInfo.className = 'pb-2.5 text-xs font-bold border-b-2 border-blue-500 text-blue-400 transition';
                btnPass.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition';
            }
        }

        function openModuleCreateModal() {
            openModal('moduleCreateModal');
        }

        function evaluateAmfCriteria() {
            const checkboxes = document.querySelectorAll('.amf-criteria');
            let count = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) count++;
            });

            const badge = document.getElementById('decisionCountBadge');
            const title = document.getElementById('amfDecisionTitle');
            const text = document.getElementById('amfDecisionText');
            const btn = document.getElementById('amfDecisionBtn');

            if (badge) badge.innerText = `${count} / 4 Kriterien erfüllt`;

            if (count === 4) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30';
                title.className = 'font-bold text-emerald-400';
                title.innerText = '✓ Stark empfohlen: Eigenständiges Modul entwickeln';
                text.innerText = 'Alle 4 Kriterien sind erfüllt! Der Bereich besitzt strategische Eigenständigkeit, messbaren Zielzustand und Governance-Bedarf.';
                btn.className = 'px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/20 whitespace-nowrap animate-pulse';
            } else if (count >= 2) {
                badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30';
                title.className = 'font-bold text-blue-400';
                title.innerText = 'ℹ️ Mögliches Modul oder Sub-Modul prüfen';
                text.innerText = 'Mehrere Kriterien sprechen für eine strukturierte Erfassung. Prüfen Sie, ob es als Untermodul eines bestehenden Bereichs genügt.';
                btn.className = 'px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm whitespace-nowrap';
            } else {
                badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-slate-800 text-slate-300 border border-dark-border';
                title.className = 'font-bold text-amber-400';
                title.innerText = '⚠️ Kein eigenes Modul empfohlen';
                text.innerText = 'Bei weniger als 2 Kriterien sollte die Aufgabe als operatives Werkzeug (Tool) oder Initiative in ein bestehendes Modul integriert werden.';
                btn.className = 'px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-slate-300 font-bold text-xs transition border border-dark-border whitespace-nowrap';
            }
        }

        function openModuleModal(moduleId) {
            const module = MODULES_DATA.find(m => m.id === moduleId);
            if (!module) return;

            const subtitles = {
                'unternehmensentwicklung': 'Company Development (Enterprise & Scaling)',
                'markenaufbau': 'Brand Building (Positioning & Authority)',
                'nachfolge': 'Succession (Generational Handover)',
                'unternehmerentwicklung': 'Entrepreneur Development (Founder & Leadership)',
                'beteiligungsmanagement': 'Investment Management (Portfolio Steering)',
                'kapitalallokation': 'Capital Allocation (Reinvestment & Liquidity)',
            };

            document.getElementById('mModalTitle').innerText = module.name;
            document.getElementById('mModalSlug').innerText = `${module.slug} • ${subtitles[module.slug] || ''}`;
            document.getElementById('mModalDescription').innerText = module.description || 'Primary structural pillar of Disavo Holding corporate development.';

            // Setup shortcut audit button inside modal
            const auditBtn = document.getElementById('mModalAuditBtn');
            if (auditBtn) {
                auditBtn.onclick = function() {
                    closeModal('moduleModal');
                    openAuditModalForModule(module.id);
                };
            }

            // 1. Goal (Zielzustand)
            const goalContainer = document.getElementById('mModalGoal');
            const goals = module.goals || [];
            if (goals.length > 0) {
                const goal = goals[0];
                goalContainer.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white text-sm">${goal.title || 'Maturity Target'}</span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Target: ${goal.target_score}%</span>
                    </div>
                    <p class="text-xs text-slate-300 mt-1.5 leading-relaxed">${goal.description || 'Target maturity state defined for this module.'}</p>
                `;
            } else {
                goalContainer.innerHTML = '<span class="text-xs text-slate-400">No specific Zielzustand recorded for this module yet.</span>';
            }

            // 2. Governing Principles
            const principlesContainer = document.getElementById('mModalPrinciples');
            const principles = module.governing_principles || module.governingPrinciples || [];
            if (principles.length > 0) {
                principlesContainer.innerHTML = principles.map(p => `
                    <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-xs">⚖️ ${p.title}</span>
                            <span class="text-[11px] font-mono text-purple-300 font-bold">v${p.version} • Active</span>
                        </div>
                        <p class="text-xs text-slate-200 italic mt-1.5">&ldquo;${p.statement}&rdquo;</p>
                        ${p.rationale ? `<p class="text-[11px] text-slate-400 mt-1">Rationale: ${p.rationale}</p>` : ''}
                    </div>
                `).join('');
            } else {
                principlesContainer.innerHTML = '<span class="text-xs text-slate-400">No active governing principles directly linked yet.</span>';
            }

            // 3. KPIs
            const kpisContainer = document.getElementById('mModalKpis');
            const kpis = module.kpis || [];
            if (kpis.length > 0) {
                kpisContainer.innerHTML = kpis.map(k => `
                    <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-xs">${k.name}</span>
                            <span class="text-[11px] font-mono text-slate-400">${k.code || ''}</span>
                        </div>
                        <div class="text-xs text-emerald-400 mt-1 font-mono font-bold">Target: ${k.target_value} ${k.unit || ''}</div>
                    </div>
                `).join('');
            } else {
                kpisContainer.innerHTML = '<span class="text-xs text-slate-400 col-span-2">No KPIs linked to this module.</span>';
            }

            // 4. Tools
            const toolsContainer = document.getElementById('mModalTools');
            const tools = module.tools || [];
            if (tools.length > 0) {
                toolsContainer.innerHTML = tools.map(t => `
                    <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border flex flex-col justify-between space-y-2">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-white text-xs">${t.name}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase font-bold">${t.type || 'Tool'}</span>
                            </div>
                            <p class="text-xs text-slate-300 mt-1 leading-relaxed">${t.description || ''}</p>
                        </div>
                        <div class="pt-2 border-t border-dark-border/60 flex justify-end">
                            <button onclick="closeModal('moduleModal'); openToolViewer(${t.id})" class="px-2.5 py-1 rounded bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[11px] font-bold transition flex items-center space-x-1">
                                <span>⚡</span>
                                <span>Öffnen / Ausführen</span>
                            </button>
                        </div>
                    </div>
                `).join('');
            } else {
                toolsContainer.innerHTML = '<span class="text-xs text-slate-400 col-span-2">Keine operativen Werkzeuge verknüpft.</span>';
            }

            const addToolBtn = document.getElementById('mModalAddToolBtn');
            if (addToolBtn) {
                addToolBtn.onclick = function() {
                    closeModal('moduleModal');
                    openToolDesignerModal(module.id);
                };
            }

            // 5. Audits
            const auditsContainer = document.getElementById('mModalAudits');
            const audits = module.audit_runs || module.auditRuns || [];
            if (audits.length > 0) {
                auditsContainer.innerHTML = audits.map(a => `
                    <div class="p-3 rounded-xl bg-dark-card border border-dark-border flex items-center justify-between text-xs">
                        <div>
                            <span class="text-white font-bold">${a.template ? a.template.name : 'Module Audit'}</span>
                            <span class="text-[11px] font-mono text-slate-400 ml-2">Status: ${a.status}</span>
                        </div>
                        <span class="font-mono font-bold text-emerald-400 text-sm">${a.overall_score !== null ? a.overall_score + '%' : 'Pending'}</span>
                    </div>
                `).join('');
            } else {
                auditsContainer.innerHTML = '<span class="text-xs text-slate-400">No audits executed for this module yet.</span>';
            }

            openModal('moduleModal');
        }

        // AUDIT MODAL LOGIC
        function openAuditModal() {
            const firstModule = MODULES_DATA[0];
            if (firstModule) {
                openAuditModalForModule(firstModule.id);
            } else {
                openModal('auditModal');
            }
        }

        function openAuditModalForModule(moduleId) {
            const runIdInput = document.getElementById('auditFormRunId');
            if (runIdInput) runIdInput.value = '';
            document.getElementById('auditModuleSelect').value = moduleId;
            onAuditModuleChanged(moduleId);
            openModal('auditModal');
        }

        function startScheduledAudit(runId, moduleId, templateId) {
            closeModal('arfModal');
            const runIdInput = document.getElementById('auditFormRunId');
            if (runIdInput) runIdInput.value = runId;
            document.getElementById('auditModuleSelect').value = moduleId;
            onAuditModuleChanged(moduleId, templateId);
            openModal('auditModal');
        }

        function onAuditModuleChanged(moduleId, forceTemplateId = null) {
            moduleId = parseInt(moduleId);
            document.getElementById('auditFormModuleId').value = moduleId;

            let template = null;
            if (forceTemplateId) {
                template = AUDIT_TEMPLATES_DATA.find(t => t.id === parseInt(forceTemplateId));
            }
            if (!template) {
                template = AUDIT_TEMPLATES_DATA.find(t => t.module_id === moduleId);
            }
            if (!template && AUDIT_TEMPLATES_DATA.length > 0) {
                template = AUDIT_TEMPLATES_DATA[0];
            }

            if (!template) {
                document.getElementById('auditQuestionsContainer').innerHTML = '<p class="text-xs text-slate-400">No audit template configured for this module.</p>';
                return;
            }

            document.getElementById('auditFormTemplateId').value = template.id;

            const questions = template.questions || [];
            if (questions.length === 0) {
                document.getElementById('auditQuestionsContainer').innerHTML = '<p class="text-xs text-slate-400">Template has no questions.</p>';
                return;
            }

            document.getElementById('auditQuestionsContainer').innerHTML = questions.map((q, idx) => `
                <div class="p-4 rounded-xl bg-dark-card border border-dark-border space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-mono text-emerald-400 font-bold uppercase">Question 0${idx + 1} (Weight: ${q.weight})</span>
                            <h5 class="text-sm font-bold text-white mt-0.5">${q.question_text}</h5>
                            ${q.guidance ? `<p class="text-xs text-slate-400 mt-1">Guidance: ${q.guidance}</p>` : ''}
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <span class="text-xs text-slate-300 font-semibold">Evaluation Score (1 to 5):</span>
                        <div class="flex items-center space-x-2">
                            ${[1, 2, 3, 4, 5].map(score => `
                                <label class="cursor-pointer">
                                    <input type="radio" name="responses[${q.id}][score]" value="${score}" ${score === 4 ? 'checked' : ''} class="peer sr-only">
                                    <span class="px-3 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700 text-xs font-mono font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-500 transition inline-block">
                                        ${score}
                                    </span>
                                </label>
                            `).join('')}
                        </div>
                    </div>

                    <div>
                        <input type="text" name="responses[${q.id}][notes]" placeholder="Optional evidence / audit notes..." class="w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-dark-border text-xs text-white focus:outline-none focus:border-emerald-500">
                    </div>
                </div>
            `).join('');
        }

        // AMF TAB SWITCHER
        function switchAmfTab(tab) {
            const secBlueprint = document.getElementById('amfSectionBlueprint');
            const secStudio = document.getElementById('amfSectionStudio');
            const btnBlueprint = document.getElementById('amfTabBtnBlueprint');
            const btnStudio = document.getElementById('amfTabBtnStudio');

            if (!secBlueprint || !secStudio) return;

            if (tab === 'studio') {
                secBlueprint.classList.add('hidden');
                secStudio.classList.remove('hidden');
                btnBlueprint.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';
                btnStudio.className = 'pb-3 border-b-2 border-amber-500 text-amber-400 font-bold transition flex items-center space-x-1.5';
            } else {
                secBlueprint.classList.remove('hidden');
                secStudio.classList.add('hidden');
                btnBlueprint.className = 'pb-3 border-b-2 border-blue-500 text-blue-400 font-bold transition flex items-center space-x-1.5';
                btnStudio.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';
            }
        }

        // ALF TAB SWITCHER
        function switchAlfTab(tab) {
            const secPrinciples = document.getElementById('alfSectionPrinciples');
            const secLearnings = document.getElementById('alfSectionLearnings');
            const secObs = document.getElementById('alfSectionObservations');

            const btnPrinciples = document.getElementById('alfTabBtnPrinciples');
            const btnLearnings = document.getElementById('alfTabBtnLearnings');
            const btnObs = document.getElementById('alfTabBtnObservations');

            if (!secPrinciples || !secLearnings || !secObs) return;

            secPrinciples.classList.add('hidden');
            secLearnings.classList.add('hidden');
            secObs.classList.add('hidden');

            btnPrinciples.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';
            btnLearnings.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';
            btnObs.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';

            if (tab === 'learnings') {
                secLearnings.classList.remove('hidden');
                btnLearnings.className = 'pb-3 border-b-2 border-amber-500 text-amber-400 font-bold transition flex items-center space-x-1.5';
            } else if (tab === 'observations') {
                secObs.classList.remove('hidden');
                btnObs.className = 'pb-3 border-b-2 border-blue-500 text-blue-400 font-bold transition flex items-center space-x-1.5';
            } else {
                secPrinciples.classList.remove('hidden');
                btnPrinciples.className = 'pb-3 border-b-2 border-purple-500 text-purple-400 font-bold transition flex items-center space-x-1.5';
            }
        }

        // ARF TAB SWITCHER
        function switchArfTab(tab) {
            const secScheduled = document.getElementById('arfSectionScheduled');
            const secHistory = document.getElementById('arfSectionHistory');
            const secReviews = document.getElementById('arfSectionReviews');

            const btnScheduled = document.getElementById('arfTabBtnScheduled');
            const btnHistory = document.getElementById('arfTabBtnHistory');
            const btnReviews = document.getElementById('arfTabBtnReviews');

            if (!secScheduled || !secHistory || !secReviews) return;

            secScheduled.classList.add('hidden');
            secHistory.classList.add('hidden');
            secReviews.classList.add('hidden');

            btnScheduled.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';
            btnHistory.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';
            btnReviews.className = 'pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition flex items-center space-x-1.5';

            if (tab === 'history') {
                secHistory.classList.remove('hidden');
                btnHistory.className = 'pb-3 border-b-2 border-blue-500 text-blue-400 font-bold transition flex items-center space-x-1.5';
            } else if (tab === 'reviews') {
                secReviews.classList.remove('hidden');
                btnReviews.className = 'pb-3 border-b-2 border-emerald-500 text-emerald-400 font-bold transition flex items-center space-x-1.5';
            } else {
                secScheduled.classList.remove('hidden');
                btnScheduled.className = 'pb-3 border-b-2 border-emerald-500 text-emerald-400 font-bold transition flex items-center space-x-1.5';
            }
        }

        // CUSTOM AUDIT DESIGNER
        function openAuditDesignerModal() {
            closeModal('arfModal');
            openModal('auditDesignerModal');
        }

        function addCustomAuditQuestionRow() {
            const container = document.getElementById('customAuditQuestionsContainer');
            if (!container) return;
            const rows = container.querySelectorAll('.custom-question-row');
            const idx = rows.length;
            const numStr = (idx + 1) < 10 ? `0${idx + 1}` : `${idx + 1}`;

            const row = document.createElement('div');
            row.className = 'custom-question-row p-3.5 rounded-xl bg-dark-card border border-dark-border space-y-2';
            row.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono text-emerald-400 font-semibold row-number">Frage ${numStr}</span>
                    <button type="button" onclick="removeCustomAuditQuestionRow(this)" class="text-xs text-slate-500 hover:text-rose-400 transition" title="Frage entfernen">✕</button>
                </div>
                <div>
                    <input type="text" name="questions[${idx}][question_text]" required placeholder="Konkrete Audit-Frage eingeben..." class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Gewichtung (1 bis 5):</label>
                        <select name="questions[${idx}][weight]" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                            <option value="1">1 (Niedrig)</option>
                            <option value="2">2 (Mittel)</option>
                            <option value="3" selected>3 (Standard)</option>
                            <option value="4">4 (Hoch)</option>
                            <option value="5">5 (Kritisch)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Bewertungshinweis (1 = Min, 5 = Max):</label>
                        <input type="text" name="questions[${idx}][guidance]" placeholder="z.B. 1=keine Dokumentation, 5=vollständig institutionalisiert" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-emerald-500 focus:outline-none">
                    </div>
                </div>
            `;
            container.appendChild(row);
        }

        function removeCustomAuditQuestionRow(btn) {
            const container = document.getElementById('customAuditQuestionsContainer');
            if (!container) return;
            const rows = container.querySelectorAll('.custom-question-row');
            if (rows.length <= 1) {
                const input = rows[0].querySelector('input[name$="[question_text]"]');
                if (input) input.value = '';
                return;
            }
            btn.closest('.custom-question-row').remove();
            const remaining = container.querySelectorAll('.custom-question-row');
            remaining.forEach((r, i) => {
                const badge = r.querySelector('.row-number');
                if (badge) {
                    const idxStr = (i + 1) < 10 ? `0${i + 1}` : `${i + 1}`;
                    badge.textContent = `Frage ${idxStr}`;
                }
                const textInput = r.querySelector('input[name*="[question_text]"]');
                if (textInput) textInput.name = `questions[${i}][question_text]`;
                const weightSelect = r.querySelector('select[name*="[weight]"]');
                if (weightSelect) weightSelect.name = `questions[${i}][weight]`;
                const guidanceInput = r.querySelector('input[name*="[guidance]"]');
                if (guidanceInput) guidanceInput.name = `questions[${i}][guidance]`;
            });
        }

        // AUDIT SCHEDULER
        function openAuditScheduleModal(moduleId = null, templateId = null) {
            closeModal('arfModal');
            if (moduleId) {
                document.getElementById('scheduleModalModuleSelect').value = moduleId;
                onScheduleModuleChanged(moduleId);
            }
            if (templateId) {
                document.getElementById('scheduleModalTemplateSelect').value = templateId;
            }
            openModal('auditScheduleModal');
        }

        function onScheduleModuleChanged(moduleId) {
            moduleId = parseInt(moduleId);
            const select = document.getElementById('scheduleModalTemplateSelect');
            if (!select) return;
            Array.from(select.options).forEach(opt => {
                const optModId = opt.getAttribute('data-module-id');
                if (!optModId || optModId == moduleId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            const firstVisible = Array.from(select.options).find(opt => opt.style.display !== 'none');
            if (firstVisible) select.value = firstVisible.value;
        }

        // TOOL DESIGNER
        function openToolDesignerModal(moduleId = null) {
            closeModal('amfModal');
            if (moduleId) {
                document.getElementById('toolDesignerModuleSelect').value = moduleId;
            }
            openModal('toolDesignerModal');
        }

        function onToolTypeChanged(type) {
            const label = document.getElementById('toolContentLabel');
            const hint = document.getElementById('toolContentHint');
            const input = document.getElementById('toolContentInput');
            if (!label || !hint || !input) return;

            if (type === 'checklist') {
                label.innerText = 'Inhalt (Punkte zeilenweise eingeben)';
                hint.innerText = '1 Zeile = 1 Checklistenpunkt';
                input.placeholder = '1. Erste Anforderung prüfen\n2. Dokumentation ablegen\n3. Freigabe durch Geschäftsleitung einholen';
            } else if (type === 'prompt') {
                label.innerText = 'KI-Prompt / Anweisungsvorlage';
                hint.innerText = 'Systematischer Prompt für LLM / Assistent';
                input.placeholder = 'Du bist ein erfahrener Unternehmensberater. Analysiere das folgende Geschäftsmodell nach AMF 1.1...';
            } else if (type === 'sop' || type === 'template') {
                label.innerText = 'Standard Operating Procedure / Textvorlage';
                hint.innerText = 'Markdown-Leitfaden oder Dokumententwurf';
                input.placeholder = '# Standard Operating Procedure (SOP)\n\n## Ziel\nKlare Beschreibung des Prozesses...\n\n## Ablauf\n1. Schritt...';
            } else {
                label.innerText = 'Link / Ressourcen-URL';
                hint.innerText = 'https://...';
                input.placeholder = 'https://tool.allocontrol.de/...';
            }
        }

        // TOOL VIEWER & RUNNER
        const ALL_TOOLS_DATA = @json($modules->pluck('tools')->flatten());

        function openToolViewer(toolId) {
            toolId = parseInt(toolId);
            const tool = ALL_TOOLS_DATA.find(t => t.id === toolId);
            if (!tool) return;

            let modName = 'Modul';
            const mod = MODULES_DATA.find(m => m.id === tool.module_id);
            if (mod) modName = mod.name;

            document.getElementById('tvTitle').innerText = tool.name;
            document.getElementById('tvBadgeModule').innerText = modName;
            document.getElementById('tvBadgeType').innerText = (tool.type || 'Tool').toUpperCase();
            document.getElementById('tvDescription').innerText = tool.description || 'Operatives Werkzeug nach AMF-Standard.';

            const progressContainer = document.getElementById('tvProgressContainer');
            const contentContainer = document.getElementById('tvContent');
            const actionsContainer = document.getElementById('tvActionButtons');

            contentContainer.innerHTML = '';
            actionsContainer.innerHTML = '';

            const content = tool.content || '';

            if (tool.type === 'checklist') {
                progressContainer.classList.remove('hidden');
                const lines = content.split('\n').map(l => l.trim()).filter(l => l.length > 0);

                if (lines.length === 0) {
                    contentContainer.innerHTML = '<p class="text-slate-400">Keine Checklisten-Punkte hinterlegt.</p>';
                    updateChecklistProgress(0, 0);
                } else {
                    contentContainer.innerHTML = `
                        <div class="space-y-2">
                            ${lines.map((line) => `
                                <label class="flex items-start space-x-3 p-3 rounded-xl bg-dark-card border border-dark-border hover:border-amber-500/40 cursor-pointer transition">
                                    <input type="checkbox" onchange="calcChecklistProgress()" class="tool-checkbox mt-0.5 w-4 h-4 rounded text-amber-500 focus:ring-amber-500 bg-dark-surface border-dark-border">
                                    <span class="text-slate-200 leading-relaxed text-xs select-none">${escapeHtml(line)}</span>
                                </label>
                            `).join('')}
                        </div>
                    `;
                    calcChecklistProgress();
                }
            } else if (tool.type === 'prompt') {
                progressContainer.classList.add('hidden');
                contentContainer.innerHTML = `
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-400 font-mono">
                            <span>KI-System-Prompt / Anweisung:</span>
                            <button onclick="copyToolText()" id="copyToolBtn" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-bold transition flex items-center space-x-1">
                                <span>📋</span>
                                <span>Kopieren</span>
                            </button>
                        </div>
                        <pre id="toolTextToCopy" class="p-4 rounded-xl bg-slate-950 border border-dark-border text-emerald-300 font-mono text-xs whitespace-pre-wrap leading-relaxed">${escapeHtml(content)}</pre>
                    </div>
                `;
            } else if (tool.type === 'saas') {
                progressContainer.classList.add('hidden');
                contentContainer.innerHTML = `
                    <div class="p-6 rounded-xl bg-dark-card border border-dark-border text-center space-y-3">
                        <span class="text-3xl">🌐</span>
                        <h4 class="text-white font-bold">Externes Software-Werkzeug</h4>
                        <p class="text-slate-300">${escapeHtml(tool.description || '')}</p>
                        <a href="${escapeHtml(content)}" target="_blank" rel="noopener noreferrer" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition shadow-md">
                            Werkzeug im neuen Tab öffnen &rarr;
                        </a>
                    </div>
                `;
            } else {
                // sop or template
                progressContainer.classList.add('hidden');
                contentContainer.innerHTML = `
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-400 font-mono">
                            <span>Dokumentation / Leitfaden:</span>
                            <button onclick="copyToolText()" id="copyToolBtn" class="px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-bold transition flex items-center space-x-1">
                                <span>📋</span>
                                <span>Inhalt kopieren</span>
                            </button>
                        </div>
                        <div id="toolTextToCopy" class="p-4 rounded-xl bg-slate-900 border border-dark-border text-slate-200 text-xs whitespace-pre-wrap leading-relaxed">${escapeHtml(content)}</div>
                    </div>
                `;
            }

            openModal('toolViewerModal');
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        function calcChecklistProgress() {
            const checkboxes = document.querySelectorAll('.tool-checkbox');
            const total = checkboxes.length;
            let checked = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) checked++;
            });
            updateChecklistProgress(checked, total);
        }

        function updateChecklistProgress(checked, total) {
            const bar = document.getElementById('tvProgressBar');
            const text = document.getElementById('tvProgressText');
            if (!bar || !text) return;
            const pct = total === 0 ? 0 : Math.round((checked / total) * 100);
            bar.style.width = `${pct}%`;
            text.innerText = `${pct}% (${checked}/${total} erledigt)`;
            if (pct === 100) {
                bar.className = 'bg-emerald-400 h-full transition-all duration-300';
                text.className = 'text-emerald-300 font-extrabold';
            } else {
                bar.className = 'bg-emerald-500 h-full transition-all duration-300';
                text.className = 'text-emerald-400 font-bold';
            }
        }

        function copyToolText() {
            const el = document.getElementById('toolTextToCopy');
            const btn = document.getElementById('copyToolBtn');
            if (!el) return;
            navigator.clipboard.writeText(el.innerText || el.textContent).then(() => {
                if (btn) {
                    btn.innerHTML = '<span>✓</span><span>Kopiert!</span>';
                    btn.className = 'px-2.5 py-1 rounded bg-emerald-600 text-white font-bold transition flex items-center space-x-1';
                    setTimeout(() => {
                        btn.innerHTML = '<span>📋</span><span>Kopieren</span>';
                        btn.className = 'px-2.5 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white font-bold transition flex items-center space-x-1';
                    }, 2000);
                }
            });
        }

        // ALF MODAL LOGIC
        function openPromoteLearningModal(learningId, title, summary) {
            closeModal('alfModal');
            document.getElementById('promoteFormLearningId').value = learningId;
            document.getElementById('promoteFormTitle').value = title;
            document.getElementById('promoteFormStatement').value = summary;
            openModal('learningPromoteModal');
        }

        function openSynthesizeLearningModal(obsId, title, content) {
            closeModal('alfModal');
            document.getElementById('synthFormObsId').value = obsId;
            document.getElementById('synthFormTitle').value = title;
            document.getElementById('synthFormSummary').value = content;
            openModal('learningSynthesizeModal');
        }

        function addReviewQuestionRow() {
            const container = document.getElementById('reviewQuestionsContainer');
            if (!container) return;
            const rows = container.querySelectorAll('.review-question-row');
            const nextIdx = rows.length + 1;
            const numStr = nextIdx < 10 ? `0${nextIdx}` : `${nextIdx}`;

            const row = document.createElement('div');
            row.className = 'review-question-row p-3.5 rounded-xl bg-dark-card border border-dark-border space-y-2';
            row.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-mono text-amber-400 font-semibold row-number">Audit-Frage ${numStr}</span>
                    <button type="button" onclick="removeReviewQuestionRow(this)" class="text-xs text-slate-500 hover:text-rose-400 transition" title="Frage entfernen">✕</button>
                </div>
                <div>
                    <input type="text" name="questions[]" placeholder="z.B. Untersuchungsschwerpunkt oder Kontrollfrage eingeben..." class="w-full px-3 py-2 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-[11px] text-slate-400 whitespace-nowrap">Modul-Zuordnung:</label>
                    <select name="question_modules[]" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                        <option value="">(Keine / Übergreifend)</option>
                        ${MODULES_DATA.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
                    </select>
                </div>
            `;
            container.appendChild(row);
        }

        function removeReviewQuestionRow(btn) {
            const container = document.getElementById('reviewQuestionsContainer');
            if (!container) return;
            const rows = container.querySelectorAll('.review-question-row');
            if (rows.length <= 1) {
                const input = rows[0].querySelector('input[name="questions[]"]');
                if (input) input.value = '';
                return;
            }
            btn.closest('.review-question-row').remove();
            const remaining = container.querySelectorAll('.review-question-row');
            remaining.forEach((r, i) => {
                const badge = r.querySelector('.row-number');
                if (badge) {
                    const idxStr = (i + 1) < 10 ? `0${i + 1}` : `${i + 1}`;
                    badge.textContent = `Audit-Frage ${idxStr}`;
                }
            });
        }

        function openCaptureModal() { openModal('captureModal'); }
        function openKpiModal() { openModal('kpiModal'); }
        function openReviewCreateModal() { 
            closeModal('arfModal');
            openModal('reviewCreateModal'); 
        }
        function openAlfModal() { openModal('alfModal'); }
        function openAmfModal() { openModal('amfModal'); }
        function openArfModal() { openModal('arfModal'); }

        // AMAR MASTER AUDIT CONDUCTOR & INSPECTOR
        function switchAmarTab(tab) {
            const secAreas = document.getElementById('amarSectionAreas');
            const secDefects = document.getElementById('amarSectionDefects');
            const secCyber = document.getElementById('amarSectionCybernetics');

            const btnAreas = document.getElementById('amarTabBtnAreas');
            const btnDefects = document.getElementById('amarTabBtnDefects');
            const btnCyber = document.getElementById('amarTabBtnCybernetics');

            if (!secAreas || !secDefects || !secCyber) return;

            secAreas.classList.add('hidden');
            secDefects.classList.add('hidden');
            secCyber.classList.add('hidden');

            btnAreas.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition whitespace-nowrap flex items-center space-x-1.5';
            btnDefects.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition whitespace-nowrap flex items-center space-x-1.5';
            btnCyber.className = 'pb-2.5 text-xs font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition whitespace-nowrap flex items-center space-x-1.5';

            if (tab === 'defects') {
                secDefects.classList.remove('hidden');
                btnDefects.className = 'pb-2.5 text-xs font-bold border-b-2 border-amber-500 text-amber-400 transition whitespace-nowrap flex items-center space-x-1.5';
            } else if (tab === 'cybernetics') {
                secCyber.classList.remove('hidden');
                btnCyber.className = 'pb-2.5 text-xs font-bold border-b-2 border-blue-500 text-blue-400 transition whitespace-nowrap flex items-center space-x-1.5';
            } else {
                secAreas.classList.remove('hidden');
                btnAreas.className = 'pb-2.5 text-xs font-bold border-b-2 border-blue-500 text-blue-400 transition whitespace-nowrap flex items-center space-x-1.5';
            }
        }

        function openAmarAuditModal(runId = null) {
            closeModal('arfModal');
            if (runId) {
                document.getElementById('amarFormRunId').value = runId;
            } else {
                document.getElementById('amarFormRunId').value = '';
            }
            switchAmarTab('areas');
            openModal('amarAuditModal');
        }

        function addAmarDefectRow() {
            const container = document.getElementById('amarDefectsContainer');
            const emptyState = document.getElementById('amarDefectsEmptyState');
            if (!container) return;

            const idx = container.querySelectorAll('.amar-defect-row').length;
            if (emptyState) emptyState.classList.add('hidden');

            const row = document.createElement('div');
            row.className = 'amar-defect-row p-4 rounded-xl bg-dark-card border border-dark-border space-y-3';
            row.innerHTML = `
                <div class="flex items-center justify-between border-b border-dark-border pb-2">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 uppercase">
                            Mangel #${idx + 1}
                        </span>
                        <span class="text-xs font-bold text-white">Strukturierte Mängelerfassung</span>
                    </div>
                    <button type="button" onclick="removeAmarDefectRow(this)" class="text-xs text-slate-400 hover:text-rose-400 transition" title="Mangel entfernen">✕ Entfernen</button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Bereich (Area):</label>
                        <select name="insight_protocols[${idx}][area]" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                            <option value="AREA 1: ONBOARDING">AREA 1: ONBOARDING</option>
                            <option value="AREA 2: AUDIT">AREA 2: AUDIT</option>
                            <option value="AREA 3: EVALUATION">AREA 3: EVALUATION</option>
                            <option value="AREA 4: AI COACH">AREA 4: AI COACH</option>
                            <option value="AREA 5: BOOKS">AREA 5: BOOKS</option>
                            <option value="AREA 6: TOOL RECOMMENDATIONS">AREA 6: TOOL RECOMMENDATIONS</option>
                            <option value="AREA 7: TOOL AUDIT">AREA 7: TOOL AUDIT</option>
                            <option value="AREA 8: IMPLEMENTATION">AREA 8: IMPLEMENTATION</option>
                            <option value="AREA 9: PROGRESS">AREA 9: PROGRESS</option>
                            <option value="AREA 10: ENTHUSIASM">AREA 10: ENTHUSIASM</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Problem (Kurzbeschreibung):</label>
                        <input type="text" name="insight_protocols[${idx}][problem]" required placeholder="z.B. Abbruch bei Tool-Auswahl" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Schweregrad (1 bis 10):</label>
                        <select name="insight_protocols[${idx}][severity]" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                            <option value="1">1 (Kosmetisch)</option>
                            <option value="2">2</option>
                            <option value="3">3 (Gering)</option>
                            <option value="4">4</option>
                            <option value="5" selected>5 (Spürbare Reibung)</option>
                            <option value="6">6</option>
                            <option value="7">7 (Hohe Reibung)</option>
                            <option value="8">8 (Schwerer Abbruch)</option>
                            <option value="9">9 (Kritisch)</option>
                            <option value="10">10 (Systemblocker)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Genaue Beobachtung:</label>
                        <textarea name="insight_protocols[${idx}][observation]" rows="2" placeholder="Was genau ist passiert?" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Auswirkung auf Nutzer:</label>
                        <textarea name="insight_protocols[${idx}][impact]" rows="2" placeholder="Verwirrung, Verzögerung, Frustration..." class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Verbesserungsmaßnahme:</label>
                        <input type="text" name="insight_protocols[${idx}][action]" placeholder="Konkreter Fix..." class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Verantwortlich:</label>
                        <input type="text" name="insight_protocols[${idx}][responsible]" placeholder="z.B. Product / Dev" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="text-[11px] text-slate-400 block mb-0.5">Messgröße / KPI:</label>
                        <input type="text" name="insight_protocols[${idx}][metric]" placeholder="z.B. Abschlussquote > 80%" class="w-full px-2.5 py-1.5 rounded-lg bg-dark-surface border border-dark-border text-white text-xs focus:border-amber-500 focus:outline-none">
                    </div>
                </div>
            `;
            container.appendChild(row);
            updateAmarDefectBadge();
        }

        function removeAmarDefectRow(btn) {
            const container = document.getElementById('amarDefectsContainer');
            const emptyState = document.getElementById('amarDefectsEmptyState');
            if (!container) return;

            btn.closest('.amar-defect-row').remove();
            const remaining = container.querySelectorAll('.amar-defect-row');
            if (remaining.length === 0 && emptyState) {
                emptyState.classList.remove('hidden');
            }
            updateAmarDefectBadge();
        }

        function updateAmarDefectBadge() {
            const container = document.getElementById('amarDefectsContainer');
            const badge = document.getElementById('amarDefectCountBadge');
            if (!container || !badge) return;
            const count = container.querySelectorAll('.amar-defect-row').length;
            badge.innerText = count;
        }

        function openAmarDetailModal(runId) {
            runId = parseInt(runId);
            const run = ALL_AUDIT_RUNS_DATA.find(r => r.id === runId);
            if (!run) return;

            const title = run.title || (run.template ? run.template.name : 'ALLOCORE MASTER AUDIT');
            document.getElementById('admTitle').innerText = title;
            
            const auditorName = run.auditor ? run.auditor.name : 'System';
            const modName = run.module ? run.module.name : 'Allocore System';
            const version = run.version || run.meta?.version || '1.0';
            document.getElementById('admSubtitle').innerText = `Auditor: ${auditorName} • Modul: ${modName} • Version: v${version}`;
            
            const dateStr = run.completed_at ? new Date(run.completed_at).toLocaleDateString('de-DE') : (run.created_at ? new Date(run.created_at).toLocaleDateString('de-DE') : '');
            document.getElementById('admDate').innerText = `Durchgeführt am ${dateStr}`;

            const scoreVal = Math.round(run.score || 0);
            document.getElementById('admScoreValue').innerText = scoreVal;

            const prevScore = run.previous_score ?? run.meta?.previous_score;
            const deltaEl = document.getElementById('admScoreDelta');
            if (prevScore !== null && prevScore !== undefined && prevScore !== '') {
                const diff = scoreVal - Math.round(prevScore);
                const sign = diff >= 0 ? '+' : '';
                deltaEl.innerText = `Vorheriges Audit: ${Math.round(prevScore)}% (${sign}${diff}%)`;
                deltaEl.className = diff >= 0 ? 'text-[11px] font-mono text-emerald-400' : 'text-[11px] font-mono text-rose-400';
            } else {
                deltaEl.innerText = 'Erstes Referenz-Audit';
                deltaEl.className = 'text-[11px] font-mono text-slate-400';
            }

            const entPassed = run.entrepreneur_test_passed ?? run.meta?.entrepreneur_test_passed;
            const entBadge = document.getElementById('admEntrepreneurTestBadge');
            if (entPassed) {
                entBadge.innerText = '✓ Bestanden';
                entBadge.className = 'inline-block px-3 py-1.5 rounded-xl font-mono text-xs font-bold bg-purple-500/20 text-purple-300 border border-purple-500/40';
            } else {
                entBadge.innerText = '⚠️ Nicht bestätigt';
                entBadge.className = 'inline-block px-3 py-1.5 rounded-xl font-mono text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40';
            }

            const featureBan = run.new_feature_ban ?? run.meta?.new_feature_ban;
            const banBadge = document.getElementById('admFeatureBanBadge');
            const banHint = document.getElementById('admFeatureBanHint');
            if (featureBan) {
                banBadge.innerText = '🚨 FEATURE BAN AKTIV';
                banBadge.className = 'inline-block px-3 py-1.5 rounded-xl font-mono text-xs font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40 animate-pulse';
                banHint.innerText = 'Keine neuen Module/Features bis Mängel behoben sind';
            } else {
                banBadge.innerText = '✓ System wertvoller';
                banBadge.className = 'inline-block px-3 py-1.5 rounded-xl font-mono text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40';
                banHint.innerText = 'Weiterentwicklung uneingeschränkt freigegeben';
            }

            const areaContainer = document.getElementById('admAreaScoresContainer');
            areaContainer.innerHTML = '';
            const areaScores = run.meta?.area_scores || {};
            const areaEntries = Object.entries(areaScores);
            if (areaEntries.length > 0) {
                areaEntries.forEach(([area, pct]) => {
                    const barColor = pct >= 80 ? 'bg-emerald-500' : (pct >= 60 ? 'bg-amber-500' : 'bg-rose-500');
                    const textColor = pct >= 80 ? 'text-emerald-400' : (pct >= 60 ? 'text-amber-400' : 'text-rose-400');
                    const row = document.createElement('div');
                    row.className = 'space-y-1';
                    row.innerHTML = `
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-200">${escapeHtml(area)}</span>
                            <span class="font-mono font-bold ${textColor}">${pct}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-dark-surface overflow-hidden">
                            <div class="${barColor} h-full transition-all duration-300" style="width: ${pct}%"></div>
                        </div>
                    `;
                    areaContainer.appendChild(row);
                });
            } else {
                areaContainer.innerHTML = '<p class="text-slate-400 text-xs italic">Keine Bereichsaufschlüsselung in den Metadaten gefunden.</p>';
            }

            const defectsContainer = document.getElementById('admDefectsContainer');
            defectsContainer.innerHTML = '';
            const defects = run.meta?.insight_protocols || [];
            if (Array.isArray(defects) && defects.length > 0) {
                defects.forEach((d, idx) => {
                    const sev = parseInt(d.severity || 5);
                    const sevColor = sev >= 8 ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : (sev >= 5 ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-blue-500/20 text-blue-300 border-blue-500/40');
                    const card = document.createElement('div');
                    card.className = 'p-3.5 rounded-xl bg-dark-surface border border-dark-border space-y-2';
                    card.innerHTML = `
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase ${sevColor} border">
                                        Schweregrad ${sev}/10
                                    </span>
                                    <span class="text-xs font-bold text-white">${escapeHtml(d.problem || 'Mangel #' + (idx + 1))}</span>
                                </div>
                                <span class="text-[11px] font-mono text-blue-400 mt-0.5 block">${escapeHtml(d.area || 'Allgemein')}</span>
                            </div>
                            <span class="text-[11px] text-slate-400 font-mono">Verantw.: <strong class="text-slate-200">${escapeHtml(d.responsible || '–')}</strong></span>
                        </div>
                        ${d.observation ? `<p class="text-xs text-slate-300"><strong class="text-slate-400">Beobachtung:</strong> ${escapeHtml(d.observation)}</p>` : ''}
                        ${d.impact ? `<p class="text-xs text-slate-300"><strong class="text-slate-400">Auswirkung:</strong> ${escapeHtml(d.impact)}</p>` : ''}
                        ${d.action ? `<div class="p-2.5 rounded-lg bg-dark-card border border-dark-border text-xs"><strong class="text-emerald-400">Maßnahme:</strong> ${escapeHtml(d.action)} ${d.metric ? `<span class="text-slate-400">(Messgröße: ${escapeHtml(d.metric)})</span>` : ''}</div>` : ''}
                    `;
                    defectsContainer.appendChild(card);
                });
            } else {
                defectsContainer.innerHTML = '<p class="text-slate-400 text-xs italic">Keine spezifischen Mängel protokolliert. Sämtliche Prüfbereiche im Sollzustand.</p>';
            }

            const cyberContainer = document.getElementById('admCyberFeedbackContainer');
            cyberContainer.innerHTML = '';
            const cyber = run.meta?.cybernetic_feedback || {};
            const cyberLabels = {
                highest_leverage: '1. Höchste Hebelwirkung auf Unternehmerwert',
                tools_to_simplify: '2. Tools zu vereinfachen / entfernen',
                user_path_dropout: '3. Häufigster Nutzerpfad-Abbruch',
                surprising_insight: '4. Überraschendste Erkenntnis',
                main_bottleneck: '5. Aktuell größter Engpass',
                top_priorities: '6. Top 3 Prioritäten bis zum nächsten Audit'
            };

            let hasCyber = false;
            Object.entries(cyberLabels).forEach(([key, label]) => {
                const ans = cyber[key];
                if (ans) {
                    hasCyber = true;
                    const item = document.createElement('div');
                    item.className = 'p-3 rounded-xl bg-dark-surface border border-dark-border space-y-1';
                    item.innerHTML = `
                        <span class="text-[11px] font-bold text-slate-400 block">${escapeHtml(label)}</span>
                        <p class="text-xs text-slate-200 whitespace-pre-wrap">${escapeHtml(ans)}</p>
                    `;
                    cyberContainer.appendChild(item);
                }
            });

            if (!hasCyber) {
                cyberContainer.innerHTML = '<p class="text-slate-400 text-xs italic col-span-2">Keine kybernetischen Reflexionseinträge vorhanden.</p>';
            }

            closeModal('arfModal');
            openModal('amarDetailModal');
        }

        const ALL_SYSTEM_MODALS = [
            'moduleModal', 'captureModal', 'auditModal', 'kpiModal',
            'reviewCreateModal', 'alfModal', 'amfModal', 'arfModal',
            'userModal', 'moduleCreateModal', 'profileModal',
            'auditDesignerModal', 'auditScheduleModal', 'toolDesignerModal',
            'toolViewerModal', 'learningPromoteModal', 'learningSynthesizeModal',
            'amarAuditModal', 'amarDetailModal'
        ];

        let highestModalZ = 50;

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            highestModalZ += 10;
            modal.style.zIndex = highestModalZ;
            modal.classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.add('hidden');

            const hasOtherOpenModals = ALL_SYSTEM_MODALS.some(id => {
                const el = document.getElementById(id);
                return el && !el.classList.contains('hidden');
            });

            if (!hasOtherOpenModals) {
                document.body.classList.remove('modal-open');
                highestModalZ = 50;
            }
        }

        window.onclick = function(event) {
            ALL_SYSTEM_MODALS.forEach(id => {
                const modal = document.getElementById(id);
                if (modal && event.target === modal) {
                    closeModal(id);
                }
            });
        };

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                ALL_SYSTEM_MODALS.forEach(id => closeModal(id));
            }
        });
    </script>
</body>
</html>
