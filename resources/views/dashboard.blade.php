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
                <!-- User Profile Badge -->
                <div class="hidden sm:flex items-center space-x-2.5 px-3 py-1.5 rounded-lg bg-dark-surface border border-dark-border">
                    <div class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 font-bold text-xs flex items-center justify-center font-mono">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-bold text-white leading-tight">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-blue-400 font-mono leading-tight">{{ auth()->user()->email }}</div>
                    </div>
                </div>

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

        <!-- Flash Success Notification Toast -->
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-950/80 border-2 border-emerald-500 text-emerald-100 flex items-center justify-between shadow-xl">
                <div class="flex items-center space-x-3">
                    <span class="w-7 h-7 rounded-full bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center text-sm">✓</span>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200 font-bold text-sm px-2">✕</button>
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

                <!-- Executive Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                    <button 
                        onclick="openCaptureModal()"
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/20 hover:shadow-blue-500/30 transition flex items-center justify-center space-x-2"
                    >
                        <span>⚡</span>
                        <span>Quick Capture</span>
                    </button>

                    <button 
                        onclick="openAuditModal()"
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/20 hover:shadow-emerald-500/30 transition flex items-center justify-center space-x-2"
                    >
                        <span>📋</span>
                        <span>Conduct Audit</span>
                    </button>

                    <button 
                        onclick="openKpiModal()"
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-md shadow-purple-600/20 hover:shadow-purple-500/30 transition flex items-center justify-center space-x-2"
                    >
                        <span>📈</span>
                        <span>Record KPI</span>
                    </button>

                    <button 
                        onclick="openReviewCreateModal()"
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-dark-hover hover:bg-slate-700 text-slate-200 border border-dark-border font-bold text-xs transition flex items-center justify-center space-x-2"
                    >
                        <span>🔄</span>
                        <span>New Review</span>
                    </button>

                    <button 
                        onclick="openUserModal()"
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/20 hover:shadow-indigo-500/30 transition flex items-center justify-center space-x-2"
                    >
                        <span>👥</span>
                        <span>Add Team Member</span>
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
                        <span class="font-bold text-white text-sm">AMF — Strategic Pillars</span>
                    </div>
                    <p class="text-slate-300 leading-relaxed">
                        Inspect each of the <strong class="text-white">6 canonical modules</strong>. Each has a defined <strong class="text-white">Zielzustand (Goal)</strong>, live <strong class="text-white">KPIs</strong>, and <strong class="text-white">Audits</strong> with weighted questions.
                    </p>
                    <div class="mt-3 pt-3 border-t border-dark-border flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Action:</span>
                        <button onclick="openAuditModal()" class="text-blue-400 hover:text-blue-300 font-semibold underline">Run Module Audit &rarr;</button>
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
                            <span class="text-xs font-mono font-bold text-purple-400 uppercase">AMF-ENGINE</span>
                            <span class="text-xs text-purple-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-purple-400 transition">Audit &amp; Reifegrad-Delta</h3>
                        <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                            Audit-Fragen nutzen datenbankbasierte Gewichtungen, um den tatsächlichen Reifegrad gegenüber dem definierten Zielzustand zu ermitteln.
                        </p>
                        <div class="mt-4 flex items-center space-x-2 text-[11px] font-mono text-slate-200">
                            <span class="px-2.5 py-0.5 rounded bg-purple-500/15 text-purple-300 border border-purple-500/30 font-medium">{{ $auditTemplates->count() }} Audit-Vorlagen</span>
                            <span class="px-2.5 py-0.5 rounded bg-slate-800 text-slate-200 border border-dark-border font-medium">{{ count($modules) }} Module</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-purple-400 font-semibold group-hover:text-purple-300 transition">
                        Audit-Bewertung &amp; Schema einsehen &rarr;
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
                        <button onclick="openKpiModal()" class="text-xs font-bold text-emerald-400 hover:underline">+ Record KPI Reading</button>
                    </div>
                    <div id="mModalKpis" class="grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
                </div>

                <!-- Operational Tools -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-amber-400 font-bold mb-2">4. Operational Tools &amp; Standard Operating Procedures</h4>
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
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-lg w-full flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-amber-400 uppercase">ARF Strategic Governance</span>
                    <h2 class="text-xl font-bold text-white mt-1">🔄 Schedule Strategic Review</h2>
                    <p class="text-xs text-slate-300 mt-1">Define review period for holding and portfolio assessments.</p>
                </div>
                <button onclick="closeModal('reviewCreateModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <form method="POST" action="{{ route('actions.review.create') }}" class="p-6 space-y-4">
                @csrf
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
                    <textarea name="summary" rows="3" required placeholder="Focus areas, key risks, and development targets to review..." class="w-full px-3.5 py-2 rounded-xl bg-dark-card border border-dark-border text-white text-sm focus:border-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-dark-border flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('reviewCreateModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-slate-300 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-xs font-bold text-white transition shadow-md shadow-amber-600/20">
                        Create Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. ALF INSPECTION MODAL -->
    <div id="alfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-purple-400 uppercase">Framework-Detailansicht</span>
                    <h2 class="text-2xl font-bold text-white mt-1">ALF — Allgemeiner Lernrahmen</h2>
                    <p class="text-xs text-slate-300 mt-1">Kognitive Nachvollziehbarkeit: Beobachtungen &rarr; Erkenntnisse &rarr; Learnings &rarr; Prinzipien</p>
                </div>
                <button onclick="closeModal('alfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-mono uppercase text-purple-400 font-bold">Aktive Prinzipien ({{ $principlesCount }})</h4>
                        <button onclick="openCaptureModal()" class="text-xs font-bold text-purple-400 hover:underline">+ Neue Beobachtung</button>
                    </div>
                    <div class="space-y-3">
                        @forelse ($principles as $principle)
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-bold text-white text-sm">{{ $principle->title }}</h5>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold">v{{ $principle->version }} • {{ $principle->state }}</span>
                                </div>
                                <p class="text-xs text-slate-200 mt-2 italic">&ldquo;{{ $principle->statement }}&rdquo;</p>
                                @if ($principle->rationale)
                                    <p class="text-xs text-slate-400 mt-1 font-sans">Rationale: {{ $principle->rationale }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">Keine Prinzipien erfasst.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end">
                <button onclick="closeModal('alfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition">Schließen</button>
            </div>
        </div>
    </div>

    <!-- 7. AMF INSPECTION MODAL -->
    <div id="amfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase">Framework-Detailansicht</span>
                    <h2 class="text-2xl font-bold text-white mt-1">AMF — Allocore Module Framework</h2>
                    <p class="text-xs text-slate-300 mt-1">Hierarchische Modulstruktur, versionierte Zielzustände &amp; Reifegrad-Messung</p>
                </div>
                <button onclick="closeModal('amfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                <div>
                    <h4 class="text-xs font-mono uppercase text-blue-400 font-bold mb-3">Die sechs kanonischen Säulen</h4>
                    <div class="space-y-3">
                        @foreach ($modules as $mod)
                            <div class="p-4 rounded-xl bg-dark-card border border-dark-border flex items-center justify-between">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="font-mono text-xs text-blue-400 font-bold">0{{ $loop->iteration }}.</span>
                                        <span class="font-bold text-white text-sm">{{ $mod->name }}</span>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-1">{{ $mod->description }}</p>
                                </div>
                                <button onclick="closeModal('amfModal'); openModuleModal({{ $mod->id }})" class="px-3 py-1.5 rounded-lg bg-blue-600/80 hover:bg-blue-600 text-white font-semibold text-xs transition">
                                    Inspizieren &rarr;
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-dark-border bg-dark-card flex justify-end">
                <button onclick="closeModal('amfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-700 text-xs font-semibold text-white transition">Schließen</button>
            </div>
        </div>
    </div>

    <!-- 8. ARF INSPECTION MODAL (Reviews & Automated Loop Closure) -->
    <div id="arfModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between bg-dark-card">
                <div>
                    <span class="text-xs font-mono font-bold text-emerald-400 uppercase">Framework-Detailansicht</span>
                    <h2 class="text-2xl font-bold text-white mt-1">ARF — Allocore Review Framework</h2>
                    <p class="text-xs text-slate-300 mt-1">Strategische Review-Zyklen &amp; automatisierte Regelkreis-Schließung (Review &rarr; Learning)</p>
                </div>
                <button onclick="closeModal('arfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition font-bold">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm">
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
                        <button onclick="openReviewCreateModal()" class="text-xs font-bold text-emerald-400 hover:underline">+ Neues Review ansetzen</button>
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

                                @if ($review->status !== 'closed')
                                    <div class="mt-4 pt-3 border-t border-dark-border flex justify-end">
                                        <form method="POST" action="{{ route('actions.review.close', $review->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-sm">
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

    <!-- CLIENT-SIDE JAVASCRIPT FOR INTERACTIVITY -->
    <script>
        const MODULES_DATA = @json($modules);
        const AUDIT_TEMPLATES_DATA = @json($auditTemplates);

        function openUserModal() {
            openModal('userModal');
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
                    <div class="p-3.5 rounded-xl bg-dark-card border border-dark-border">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-white text-xs">${t.name}</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-500/20 text-blue-300 uppercase font-bold">${t.type || 'Tool'}</span>
                        </div>
                        <p class="text-xs text-slate-300 mt-1.5">${t.description || ''}</p>
                    </div>
                `).join('');
            } else {
                toolsContainer.innerHTML = '<span class="text-xs text-slate-400 col-span-2">No operational tools attached yet.</span>';
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
            document.getElementById('auditModuleSelect').value = moduleId;
            onAuditModuleChanged(moduleId);
            openModal('auditModal');
        }

        function onAuditModuleChanged(moduleId) {
            moduleId = parseInt(moduleId);
            document.getElementById('auditFormModuleId').value = moduleId;

            let template = AUDIT_TEMPLATES_DATA.find(t => t.module_id === moduleId);
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

        function openCaptureModal() { openModal('captureModal'); }
        function openKpiModal() { openModal('kpiModal'); }
        function openReviewCreateModal() { openModal('reviewCreateModal'); }
        function openAlfModal() { openModal('alfModal'); }
        function openAmfModal() { openModal('amfModal'); }
        function openArfModal() { openModal('arfModal'); }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.classList.remove('modal-open');
        }

        window.onclick = function(event) {
            ['moduleModal', 'captureModal', 'auditModal', 'kpiModal', 'reviewCreateModal', 'alfModal', 'amfModal', 'arfModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (event.target === modal) {
                    closeModal(id);
                }
            });
        };

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                ['moduleModal', 'captureModal', 'auditModal', 'kpiModal', 'reviewCreateModal', 'alfModal', 'amfModal', 'arfModal'].forEach(id => closeModal(id));
            }
        });
    </script>
</body>
</html>
