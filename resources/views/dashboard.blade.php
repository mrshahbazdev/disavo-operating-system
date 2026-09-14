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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0c0e14; }
        .modal-open { overflow: hidden; }
    </style>
</head>
<body class="bg-[#0c0e14] text-slate-200 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="border-b border-dark-border bg-dark-surface/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm font-mono shadow hover:bg-blue-500 transition">
                    D
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-sm tracking-tight text-white">DOS</span>
                        <span class="text-slate-500 font-mono text-xs">/</span>
                        <span class="text-xs font-semibold text-slate-300">{{ $tenant->name ?? 'Disavo Holding GmbH' }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-semibold text-white">{{ auth()->user()->name }}</div>
                    <div class="text-[11px] text-slate-400 font-mono">{{ auth()->user()->email }}</div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-lg border border-dark-border hover:bg-dark-hover text-xs font-medium text-slate-300 hover:text-white transition">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full space-y-8">
        
        <!-- Welcome Banner -->
        <div class="p-6 rounded-2xl bg-dark-surface border border-dark-border flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <div class="inline-block px-2.5 py-0.5 rounded bg-blue-500/10 text-blue-400 text-[11px] font-semibold mb-2">
                    EXECUTIVE PORTAL
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Disavo Operating System (DOS)</h1>
                <p class="text-xs text-slate-400 mt-1">Click on any Module or Engine below to inspect full operational details, principles, and reviews.</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <span class="px-3 py-1.5 rounded-lg bg-[#090b10] border border-dark-border text-xs font-mono text-emerald-400 flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Knowledge Graph Active</span>
                </span>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Strategic Modules</div>
                <div class="text-3xl font-bold font-mono text-white mt-2">{{ count($modules) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">AMF Adjacency Tree</div>
            </div>

            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Governing Principles</div>
                <div class="text-3xl font-bold font-mono text-blue-400 mt-2">{{ $principlesCount }}</div>
                <div class="text-[11px] text-slate-500 mt-1">ALF Active Laws</div>
            </div>

            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Active Graph Edges</div>
                <div class="text-3xl font-bold font-mono text-emerald-400 mt-2">{{ $edgesCount }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Append-Only Relations</div>
            </div>

            <div class="p-5 rounded-xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Reviews Closed</div>
                <div class="text-3xl font-bold font-mono text-purple-400 mt-2">{{ $reviewsCount }}</div>
                <div class="text-[11px] text-slate-500 mt-1">ARF Strategic Cadence</div>
            </div>
        </div>

        <!-- The 6 Canonical Modules Grid -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">Canonical Development Modules</h2>
                    <p class="text-xs text-slate-400">Click any module card or &ldquo;Inspect &rarr;&rdquo; to view goals, KPIs, tools, and governing principles.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($modules as $module)
                    <div 
                        onclick="openModuleModal({{ $module->id }})"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[10px] font-mono text-slate-400 uppercase">Module {{ $loop->iteration }}</span>
                                    <h3 class="text-base font-bold text-white mt-0.5 group-hover:text-blue-400 transition">{{ $module->name }}</h3>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    Active
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2">
                                {{ $module->description ?? 'Primary pillar of Disavo Holding corporate development.' }}
                            </p>

                            <!-- Quick Badges -->
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @if ($module->goals->count() > 0)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Goal: {{ $module->goals->first()->target_score }}%
                                    </span>
                                @endif
                                @if ($module->kpis->count() > 0)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300 border border-dark-border">
                                        {{ $module->kpis->count() }} KPIs
                                    </span>
                                @endif
                                @if ($module->tools->count() > 0)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-slate-800 text-slate-300 border border-dark-border">
                                        {{ $module->tools->count() }} Tools
                                    </span>
                                @endif
                                @if (!empty($module->governing_principles) && $module->governing_principles->count() > 0)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                        Governed
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-dark-border/60 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Slug: <code class="font-mono text-slate-300">{{ $module->slug }}</code></span>
                            <span class="text-blue-400 font-semibold group-hover:translate-x-0.5 transition inline-flex items-center space-x-1">
                                <span>Inspect</span>
                                <span>&rarr;</span>
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3 Pillars Overview (Clickable Cards) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-white tracking-tight">Core System Engines (ALF / AMF / ARF)</h2>
                    <p class="text-xs text-slate-400">Click any engine card to inspect live data, cognitive trails, audit templates, and loop closure.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- ALF Engine Card -->
                <div 
                    onclick="openAlfModal()"
                    class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-blue-400 uppercase">ALF Engine</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-blue-400 transition">Cognitive Traceability</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Decisions are verified backwards to empirical Observations. Principles update non-destructively through versioned Supersedes relationships.
                        </p>
                        <div class="mt-4 flex items-center space-x-2 text-[11px] font-mono text-slate-300">
                            <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-300 border border-blue-500/20">{{ $principles->count() }} Principles</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">{{ $learnings->count() }} Learnings</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-blue-400 font-medium">
                        Click to view Principles &amp; Cognitive Trail &rarr;
                    </div>
                </div>

                <!-- AMF Engine Card -->
                <div 
                    onclick="openAmfModal()"
                    class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-purple-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-purple-400 uppercase">AMF Engine</span>
                            <span class="text-xs text-purple-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-purple-400 transition">Audit &amp; Maturity Delta</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Audit questions use database-configured weights to evaluate actual maturity against target state (Zielzustand).
                        </p>
                        <div class="mt-4 flex items-center space-x-2 text-[11px] font-mono text-slate-300">
                            <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20">{{ $auditTemplates->count() }} Audit Templates</span>
                            <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-dark-border">{{ count($modules) }} Modules</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-purple-400 font-medium">
                        Click to view Audit Scoring &amp; Schema &rarr;
                    </div>
                </div>

                <!-- ARF Engine Card -->
                <div 
                    onclick="openArfModal()"
                    class="p-6 rounded-2xl bg-dark-surface border border-dark-border hover:border-emerald-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-emerald-400 uppercase">ARF Engine</span>
                            <span class="text-xs text-emerald-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1 group-hover:text-emerald-400 transition">Loop Closure</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Closing a Review triggers SpawnLearningFromReview, creating a Draft Learning in ALF to ensure continuous organizational evolution.
                        </p>
                        <div class="mt-4 flex items-center space-x-2 text-[11px] font-mono text-slate-300">
                            <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">{{ $reviews->count() }} Reviews</span>
                            <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-300 border border-blue-500/20">Auto-Spawning Active</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-dark-border text-xs text-emerald-400 font-medium">
                        Click to inspect Reviews &amp; Loop Closure &rarr;
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
    <div id="moduleModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <!-- Modal Header -->
            <div class="p-6 border-b border-dark-border flex items-start justify-between">
                <div>
                    <div class="flex items-center space-x-2">
                        <span id="mModalNumber" class="text-xs font-mono font-bold text-blue-400 uppercase">Module</span>
                        <span class="text-slate-500">•</span>
                        <span id="mModalSlug" class="text-xs font-mono text-slate-400">slug</span>
                    </div>
                    <h2 id="mModalTitle" class="text-2xl font-bold text-white mt-1">Module Name</h2>
                    <p id="mModalDescription" class="text-xs text-slate-400 mt-1 leading-relaxed">Description</p>
                </div>
                <button onclick="closeModal('moduleModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition">
                    ✕
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                
                <!-- Zielzustand (Target Goal) -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Target State (Zielzustand)</h4>
                    <div id="mModalGoal" class="p-4 rounded-xl bg-[#090b10] border border-dark-border">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Governing Principles -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Governing Principles (ALF Law)</h4>
                    <div id="mModalPrinciples" class="space-y-2">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- KPIs -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Key Performance Indicators (KPIs)</h4>
                    <div id="mModalKpis" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Operational Tools -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Operational Tools (Werkzeuge)</h4>
                    <div id="mModalTools" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Recent Audits -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Recent Audits</h4>
                    <div id="mModalAudits" class="space-y-2">
                        <!-- Filled by JS -->
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-dark-border bg-[#090b10] flex justify-end">
                <button onclick="closeModal('moduleModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-800 text-xs font-semibold text-white transition">
                    Close Details
                </button>
            </div>
        </div>
    </div>

    <!-- 2. ALF COGNITIVE TRACEABILITY MODAL -->
    <div id="alfModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between">
                <div>
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase">Framework Deep-Dive</span>
                    <h2 class="text-2xl font-bold text-white mt-1">ALF — Allocore Learning Framework</h2>
                    <p class="text-xs text-slate-400 mt-1">Full cognitive trail: Observation &rarr; Insight &rarr; Learning &rarr; Principle &larr; Decision</p>
                </div>
                <button onclick="closeModal('alfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                
                <!-- Cognitive Chain Visual -->
                <div class="p-4 rounded-xl bg-[#090b10] border border-dark-border font-mono text-xs">
                    <div class="text-[11px] text-slate-400 uppercase font-semibold mb-2">Cognitive Traceability Flow:</div>
                    <div class="flex flex-wrap items-center gap-2 text-slate-300">
                        <span class="px-2.5 py-1 rounded bg-blue-500/10 text-blue-400 border border-blue-500/20">Observation</span>
                        <span>&rarr;</span>
                        <span class="px-2.5 py-1 rounded bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Insight</span>
                        <span>&rarr;</span>
                        <span class="px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Learning (Validated)</span>
                        <span>&rarr;</span>
                        <span class="px-2.5 py-1 rounded bg-purple-500/10 text-purple-400 border border-purple-500/20">Principle (Active)</span>
                        <span>&larr;</span>
                        <span class="px-2.5 py-1 rounded bg-amber-500/10 text-amber-400 border border-amber-500/20">Decision</span>
                    </div>
                </div>

                <!-- Active Principles List -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Active Governing Principles ({{ $principles->count() }})</h4>
                    <div class="space-y-3">
                        @forelse ($principles as $principle)
                            <div class="p-4 rounded-xl bg-[#090b10] border border-dark-border">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-bold text-white text-sm">{{ $principle->title }}</h5>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                        v{{ $principle->version }} [{{ is_object($principle->state) ? $principle->state->name() : $principle->state }}]
                                    </span>
                                </div>
                                <p class="text-xs text-slate-300 mt-2 font-medium italic">&ldquo;{{ $principle->statement }}&rdquo;</p>
                                @if ($principle->rationale)
                                    <p class="text-[11px] text-slate-400 mt-1.5">{{ $principle->rationale }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No principles recorded.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Validated Learnings List -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Organizational Learnings ({{ $learnings->count() }})</h4>
                    <div class="space-y-2.5">
                        @forelse ($learnings as $learning)
                            <div class="p-3.5 rounded-xl bg-[#090b10] border border-dark-border flex items-start justify-between">
                                <div>
                                    <h5 class="font-semibold text-white text-xs">{{ $learning->title }}</h5>
                                    <p class="text-[11px] text-slate-400 mt-1">{{ $learning->summary }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 shrink-0 ml-2">
                                    {{ is_object($learning->state) ? $learning->state->name() : $learning->state }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No learnings recorded.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="p-4 border-t border-dark-border bg-[#090b10] flex justify-end">
                <button onclick="closeModal('alfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-800 text-xs font-semibold text-white transition">Close</button>
            </div>
        </div>
    </div>

    <!-- 3. AMF AUDIT & MATURITY DELTA MODAL -->
    <div id="amfModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between">
                <div>
                    <span class="text-xs font-mono font-bold text-purple-400 uppercase">Framework Deep-Dive</span>
                    <h2 class="text-2xl font-bold text-white mt-1">AMF — Allocore Module Framework</h2>
                    <p class="text-xs text-slate-400 mt-1">Module Adjacency Hierarchy, DB-Weighted Audit Engine, &amp; Maturity Gap Scoring</p>
                </div>
                <button onclick="closeModal('amfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                
                <!-- Scoring Formula Explainer -->
                <div class="p-4 rounded-xl bg-[#090b10] border border-dark-border">
                    <h4 class="text-xs font-mono font-bold text-purple-400 uppercase mb-1">Maturity Scoring Engine:</h4>
                    <p class="text-xs text-slate-300 font-mono mt-1">
                        Score = ( &Sigma; (actual / max &times; weight) / &Sigma; weight ) &times; 100
                    </p>
                    <p class="text-[11px] text-slate-400 mt-2">
                        Questions have weights stored in the database (never hardcoded). The audit score is compared against the versioned module Zielzustand to calculate the exact maturity delta.
                    </p>
                </div>

                <!-- Audit Templates & Weighted Questions -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Configured Audit Templates ({{ $auditTemplates->count() }})</h4>
                    <div class="space-y-4">
                        @forelse ($auditTemplates as $template)
                            <div class="p-4 rounded-xl bg-[#090b10] border border-dark-border">
                                <div class="flex items-center justify-between">
                                    <h5 class="font-bold text-white text-sm">{{ $template->name }}</h5>
                                    <span class="text-[10px] font-mono text-purple-400">{{ $template->questions->count() }} Weighted Questions</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-1">{{ $template->description }}</p>

                                <div class="mt-3 space-y-2">
                                    @foreach ($template->questions as $question)
                                        <div class="p-2.5 rounded-lg bg-dark-surface border border-dark-border/60 flex items-start justify-between gap-2 text-xs">
                                            <span class="text-slate-300">{{ $question->text }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-purple-500/10 text-purple-300 border border-purple-500/20 shrink-0">
                                                Weight: {{ $question->weight }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No audit templates configured.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="p-4 border-t border-dark-border bg-[#090b10] flex justify-end">
                <button onclick="closeModal('amfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-800 text-xs font-semibold text-white transition">Close</button>
            </div>
        </div>
    </div>

    <!-- 4. ARF REVIEW LOOPS & LOOP CLOSURE MODAL -->
    <div id="arfModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between">
                <div>
                    <span class="text-xs font-mono font-bold text-emerald-400 uppercase">Framework Deep-Dive</span>
                    <h2 class="text-2xl font-bold text-white mt-1">ARF — Allocore Review Framework</h2>
                    <p class="text-xs text-slate-400 mt-1">Strategic Review Cadence &amp; Automated Loop Closure (Review &rarr; Learning)</p>
                </div>
                <button onclick="closeModal('arfModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 text-sm">
                
                <!-- Loop Closure Explainer -->
                <div class="p-4 rounded-xl bg-[#090b10] border border-emerald-500/30">
                    <h4 class="text-xs font-mono font-bold text-emerald-400 uppercase mb-1">How The Loop Closes:</h4>
                    <p class="text-xs text-slate-300 mt-1">
                        When an executive marks a Strategic Review as <strong class="text-white">Closed</strong>, DOS executes <code class="font-mono text-emerald-400">CloseReview</code> &rarr; <code class="font-mono text-emerald-400">SpawnLearningFromReview</code>.
                    </p>
                    <p class="text-[11px] text-slate-400 mt-2">
                        This automatically creates a new <code class="text-slate-300">Draft</code> Learning in ALF linked by a <code class="font-mono text-emerald-300">Review -(generates)-&gt; Learning</code> edge. Stewards validate it and promote it to an active Principle that governs a Module, ensuring knowledge evolution never stops.
                    </p>
                </div>

                <!-- Reviews List -->
                <div>
                    <h4 class="text-xs font-mono uppercase text-slate-400 font-semibold mb-2">Recorded Strategic Reviews ({{ $reviews->count() }})</h4>
                    <div class="space-y-3">
                        @forelse ($reviews as $review)
                            <div class="p-4 rounded-xl bg-[#090b10] border border-dark-border">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="font-bold text-white text-sm">{{ $review->title }}</h5>
                                        <span class="text-xs text-slate-400 font-mono">Period: {{ $review->period }} • Date: {{ $review->review_date?->format('Y-m-d') }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $review->status === 'closed' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                        {{ $review->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-300 mt-2">{{ $review->summary }}</p>

                                @if ($review->items->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-dark-border/60">
                                        <span class="text-[10px] font-mono text-slate-400 uppercase font-semibold block mb-1.5">Review Items:</span>
                                        <div class="space-y-1">
                                            @foreach ($review->items as $item)
                                                <div class="text-xs flex items-center justify-between p-2 rounded bg-dark-surface border border-dark-border/40">
                                                    <span class="text-slate-300">{{ $item->topic }}</span>
                                                    <span class="text-[10px] font-mono text-slate-400">{{ $item->module?->name ?? 'Global' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-500">No reviews recorded.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="p-4 border-t border-dark-border bg-[#090b10] flex justify-end">
                <button onclick="closeModal('arfModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-800 text-xs font-semibold text-white transition">Close</button>
            </div>
        </div>
    </div>

    <!-- Data Injection for Client-Side Modal population -->
    <script>
        const MODULES_DATA = @json($modules);

        function openModuleModal(moduleId) {
            const module = MODULES_DATA.find(m => m.id === moduleId);
            if (!module) return;

            document.getElementById('mModalTitle').innerText = module.name;
            document.getElementById('mModalSlug').innerText = module.slug;
            document.getElementById('mModalDescription').innerText = module.description || 'Primary structural pillar of Disavo Holding corporate development.';

            // Goal
            const goalContainer = document.getElementById('mModalGoal');
            if (module.goals && module.goals.length > 0) {
                const goal = module.goals[0];
                goalContainer.innerHTML = `
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white">${goal.title || 'Maturity Target'}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">Target: ${goal.target_score}%</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">${goal.description || 'Target maturity state defined for this module.'}</p>
                `;
            } else {
                goalContainer.innerHTML = '<span class="text-xs text-slate-500">No specific Zielzustand recorded for this module yet.</span>';
            }

            // Governing Principles
            const principlesContainer = document.getElementById('mModalPrinciples');
            if (module.governing_principles && module.governing_principles.length > 0) {
                principlesContainer.innerHTML = module.governing_principles.map(p => `
                    <div class="p-3 rounded-lg bg-[#090b10] border border-dark-border">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white text-xs">${p.title}</span>
                            <span class="text-[10px] font-mono text-purple-400">v${p.version}</span>
                        </div>
                        <p class="text-[11px] text-slate-300 italic mt-1">&ldquo;${p.statement}&rdquo;</p>
                    </div>
                `).join('');
            } else {
                principlesContainer.innerHTML = '<span class="text-xs text-slate-500">No active governing principles directly linked yet.</span>';
            }

            // KPIs
            const kpisContainer = document.getElementById('mModalKpis');
            if (module.kpis && module.kpis.length > 0) {
                kpisContainer.innerHTML = module.kpis.map(k => `
                    <div class="p-3 rounded-lg bg-[#090b10] border border-dark-border">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white text-xs">${k.name}</span>
                            <span class="text-[10px] font-mono text-slate-400">${k.code || ''}</span>
                        </div>
                        <div class="text-[11px] text-emerald-400 mt-1 font-mono">Target: ${k.target_value} ${k.unit || ''}</div>
                    </div>
                `).join('');
            } else {
                kpisContainer.innerHTML = '<span class="text-xs text-slate-500 col-span-2">No KPIs linked to this module.</span>';
            }

            // Tools
            const toolsContainer = document.getElementById('mModalTools');
            if (module.tools && module.tools.length > 0) {
                toolsContainer.innerHTML = module.tools.map(t => `
                    <div class="p-3 rounded-lg bg-[#090b10] border border-dark-border">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-white text-xs">${t.name}</span>
                            <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-blue-500/10 text-blue-400 uppercase">${t.type || 'Tool'}</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">${t.description || ''}</p>
                    </div>
                `).join('');
            } else {
                toolsContainer.innerHTML = '<span class="text-xs text-slate-500 col-span-2">No operational tools attached yet.</span>';
            }

            // Audits
            const auditsContainer = document.getElementById('mModalAudits');
            if (module.audit_runs && module.audit_runs.length > 0) {
                auditsContainer.innerHTML = module.audit_runs.map(a => `
                    <div class="p-2.5 rounded-lg bg-[#090b10] border border-dark-border flex items-center justify-between text-xs">
                        <div>
                            <span class="text-white font-medium">${a.template ? a.template.name : 'Module Audit'}</span>
                            <span class="text-[10px] font-mono text-slate-500 ml-2">Status: ${a.status}</span>
                        </div>
                        <span class="font-mono font-bold text-emerald-400">${a.overall_score !== null ? a.overall_score + '%' : 'Pending'}</span>
                    </div>
                `).join('');
            } else {
                auditsContainer.innerHTML = '<span class="text-xs text-slate-500">No audits executed for this module yet.</span>';
            }

            openModal('moduleModal');
        }

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

        // Close on clicking backdrop
        window.onclick = function(event) {
            ['moduleModal', 'alfModal', 'amfModal', 'arfModal'].forEach(id => {
                const modal = document.getElementById(id);
                if (event.target === modal) {
                    closeModal(id);
                }
            });
        };

        // Close on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                ['moduleModal', 'alfModal', 'amfModal', 'arfModal'].forEach(id => closeModal(id));
            }
        });
    </script>

</body>
</html>
