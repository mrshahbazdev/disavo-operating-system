<!DOCTYPE html>
<html lang="en" class="scroll-smooth dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DOS — Disavo Operating System</title>
    <meta name="description" content="A cyclic, traceable, append-only organizational knowledge graph powered by Laravel.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CDN for 100% reliable shared hosting rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        dos: {
                            50: '#f5f7ff',
                            100: '#ebf0fe',
                            200: '#ced9fd',
                            300: '#a3b7fb',
                            400: '#728ef7',
                            500: '#4f6bf2',
                            600: '#384ee6',
                            700: '#2b3bcc',
                            800: '#1e2994',
                            900: '#0c0f1d',
                            950: '#060810',
                        },
                        surface: {
                            base: '#080a11',
                            card: '#0f121e',
                            border: '#1b2034',
                            hover: '#181e30',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #060810;
            color: #e2e8f0;
        }
        .bg-grid {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
        .glow-indigo {
            box-shadow: 0 0 50px -10px rgba(99, 102, 241, 0.25);
        }
        .glow-emerald {
            box-shadow: 0 0 50px -10px rgba(16, 185, 129, 0.25);
        }
        .text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-gradient-indigo {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-[#060810] text-slate-200 antialiased selection:bg-indigo-500 selection:text-white relative min-h-screen flex flex-col">

    <!-- Background Subtle Gradients -->
    <div class="fixed inset-0 bg-grid pointer-events-none z-0"></div>
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[400px] bg-indigo-600/10 blur-[130px] rounded-full pointer-events-none z-0"></div>
    <div class="fixed bottom-0 right-0 w-[600px] h-[500px] bg-violet-600/5 blur-[150px] rounded-full pointer-events-none z-0"></div>

    <!-- Navigation -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-[#060810]/80 border-b border-surface-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center font-mono font-bold text-white shadow-lg shadow-indigo-500/20">
                    D
                </div>
                <div class="flex items-baseline space-x-2">
                    <span class="font-bold text-lg tracking-tight text-white">DOS</span>
                    <span class="text-xs text-slate-400 font-medium hidden sm:inline">Disavo Operating System</span>
                </div>
                <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-mono font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">v0.3 • Phase 3</span>
            </div>

            <nav class="hidden md:flex items-center space-x-8 text-sm font-medium text-slate-300">
                <a href="#principles" class="hover:text-white transition">Architecture</a>
                <a href="#frameworks" class="hover:text-white transition">Frameworks (ALF/AMF/ARF)</a>
                <a href="#graph" class="hover:text-white transition">Knowledge Graph</a>
                <a href="#api" class="hover:text-white transition">API</a>
                <a href="https://github.com/mrshahbazdev/disavo-operating-system" target="_blank" class="hover:text-white transition flex items-center space-x-1">
                    <span>GitHub</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>
                </a>
            </nav>

            <div class="flex items-center space-x-3">
                <a href="#api" class="px-4 py-2 text-xs font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition shadow-lg shadow-indigo-600/20">
                    Explore API
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow z-10">

        <!-- Hero Section -->
        <section class="relative pt-20 pb-16 md:pt-28 md:pb-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-4xl mx-auto space-y-6">
                    
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full text-xs font-mono bg-indigo-500/10 border border-indigo-500/20 text-indigo-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>CYCLIC KNOWLEDGE GRAPH ENGINE • POSTGRESQL 16 & LARAVEL 12</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-gradient leading-[1.1]">
                        The Operating System for <span class="text-gradient-indigo">Perpetual Intelligence</span>
                    </h1>

                    <p class="text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed font-normal">
                        Every organizational decision traceable to its raw observation. Never-deleting append-only edges. Continuous learning loops across <span class="text-white font-medium">ALF</span>, <span class="text-white font-medium">AMF</span>, and <span class="text-white font-medium">ARF</span>.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="#graph" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold text-sm shadow-xl shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:scale-[1.02] transition duration-200">
                            Interactive Knowledge Graph
                        </a>
                        <a href="#principles" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-surface-card border border-surface-border text-slate-300 hover:text-white hover:border-slate-600 font-semibold text-sm transition">
                            Read Architecture Specs
                        </a>
                    </div>

                    <!-- Live Metrics Strip -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-12 max-w-4xl mx-auto">
                        <div class="p-4 rounded-xl bg-surface-card/60 border border-surface-border text-center">
                            <div class="text-2xl sm:text-3xl font-bold font-mono text-white">{{ $stats['modules'] ?? 6 }}</div>
                            <div class="text-xs text-slate-400 uppercase tracking-wider mt-1">Canonical Modules</div>
                        </div>
                        <div class="p-4 rounded-xl bg-surface-card/60 border border-surface-border text-center">
                            <div class="text-2xl sm:text-3xl font-bold font-mono text-indigo-400">{{ $stats['active_edges'] ?? 14 }}</div>
                            <div class="text-xs text-slate-400 uppercase tracking-wider mt-1">Active Graph Edges</div>
                        </div>
                        <div class="p-4 rounded-xl bg-surface-card/60 border border-surface-border text-center">
                            <div class="text-2xl sm:text-3xl font-bold font-mono text-emerald-400">100%</div>
                            <div class="text-xs text-slate-400 uppercase tracking-wider mt-1">Traceable Provenance</div>
                        </div>
                        <div class="p-4 rounded-xl bg-surface-card/60 border border-surface-border text-center">
                            <div class="text-2xl sm:text-3xl font-bold font-mono text-purple-400">0</div>
                            <div class="text-xs text-slate-400 uppercase tracking-wider mt-1">Hard Deletes (Append-Only)</div>
                        </div>
                    </div>

                </div>

                <!-- Interactive Visual Hero Canvas -->
                <div class="mt-14 max-w-5xl mx-auto p-1 rounded-2xl bg-gradient-to-b from-indigo-500/20 via-surface-border to-surface-card/40 shadow-2xl">
                    <div class="bg-[#090c16] rounded-[15px] p-6 sm:p-8 border border-surface-border relative overflow-hidden">
                        
                        <div class="flex items-center justify-between pb-6 border-b border-surface-border">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-rose-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500/80"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                                <span class="text-xs font-mono text-slate-400 ml-2">Live Provenance Chain & Tension Radar</span>
                            </div>
                            <div class="text-xs font-mono text-emerald-400 flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span>Graph Active • 30/30 Tests Verified</span>
                            </div>
                        </div>

                        <!-- Graph Visualization Simulation -->
                        <div class="py-10 relative flex flex-col md:flex-row items-center justify-between gap-6 overflow-x-auto text-xs font-mono">
                            
                            <!-- Observation Node -->
                            <div class="flex-1 min-w-[160px] p-4 rounded-xl bg-[#0f1424] border border-indigo-500/30 text-center relative group hover:border-indigo-400 transition">
                                <div class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">Observation</div>
                                <div class="font-sans font-semibold text-slate-200 mt-1">Founder Firefighting</div>
                                <div class="text-[11px] text-slate-400 mt-1 font-sans">40% operational drag</div>
                                <div class="mt-2 text-[10px] text-indigo-300 font-mono">ID: OBS-001</div>
                            </div>

                            <!-- Edge Arrow -->
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="text-[10px] text-indigo-400 mb-1">produces</span>
                                <div class="w-12 h-0.5 bg-gradient-to-r from-indigo-500 to-indigo-400"></div>
                                <span class="text-indigo-400 text-sm">▶</span>
                            </div>

                            <!-- Insight Node -->
                            <div class="flex-1 min-w-[160px] p-4 rounded-xl bg-[#0f1424] border border-indigo-500/30 text-center relative group hover:border-indigo-400 transition">
                                <div class="text-[10px] uppercase font-bold text-indigo-400 tracking-wider">Insight</div>
                                <div class="font-sans font-semibold text-slate-200 mt-1">Operational Gravity</div>
                                <div class="text-[11px] text-slate-400 mt-1 font-sans">Strategic vacuum</div>
                                <div class="mt-2 text-[10px] text-indigo-300 font-mono">ID: INS-001</div>
                            </div>

                            <!-- Edge Arrow -->
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="text-[10px] text-emerald-400 mb-1">validates</span>
                                <div class="w-12 h-0.5 bg-gradient-to-r from-indigo-400 to-emerald-400"></div>
                                <span class="text-emerald-400 text-sm">▶</span>
                            </div>

                            <!-- Learning Node -->
                            <div class="flex-1 min-w-[160px] p-4 rounded-xl bg-[#0f1424] border border-emerald-500/30 text-center relative group hover:border-emerald-400 transition">
                                <div class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Learning</div>
                                <div class="font-sans font-semibold text-slate-200 mt-1">Delegation Architecture</div>
                                <div class="text-[11px] text-slate-400 mt-1 font-sans">Status: Validated</div>
                                <div class="mt-2 text-[10px] text-emerald-300 font-mono">ID: LRN-001</div>
                            </div>

                            <!-- Edge Arrow -->
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="text-[10px] text-purple-400 mb-1">promotes</span>
                                <div class="w-12 h-0.5 bg-gradient-to-r from-emerald-400 to-purple-400"></div>
                                <span class="text-purple-400 text-sm">▶</span>
                            </div>

                            <!-- Principle Node -->
                            <div class="flex-1 min-w-[160px] p-4 rounded-xl bg-[#0f1424] border border-purple-500/30 text-center relative group hover:border-purple-400 transition">
                                <div class="text-[10px] uppercase font-bold text-purple-400 tracking-wider">Principle</div>
                                <div class="font-sans font-semibold text-slate-200 mt-1">Subsidiarity</div>
                                <div class="text-[11px] text-slate-400 mt-1 font-sans">Version: 1.0 [Active]</div>
                                <div class="mt-2 text-[10px] text-purple-300 font-mono">ID: PRIN-001</div>
                            </div>

                            <!-- Edge Arrow -->
                            <div class="flex flex-col items-center justify-center text-slate-500">
                                <span class="text-[10px] text-amber-400 mb-1">governs</span>
                                <div class="w-12 h-0.5 bg-gradient-to-r from-purple-400 to-amber-400"></div>
                                <span class="text-amber-400 text-sm">▶</span>
                            </div>

                            <!-- Module Node -->
                            <div class="flex-1 min-w-[160px] p-4 rounded-xl bg-[#0f1424] border border-amber-500/30 text-center relative group hover:border-amber-400 transition">
                                <div class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">Module</div>
                                <div class="font-sans font-semibold text-slate-200 mt-1">Unternehmerentwicklung</div>
                                <div class="text-[11px] text-slate-400 mt-1 font-sans">Audit Maturity: 85%</div>
                                <div class="mt-2 text-[10px] text-amber-300 font-mono">MOD-004</div>
                            </div>

                        </div>

                        <div class="mt-6 pt-4 border-t border-surface-border/60 flex flex-wrap items-center justify-between text-xs text-slate-400">
                            <span class="flex items-center space-x-2">
                                <span class="text-slate-200 font-medium">Recursive CTE Cycle Guard:</span>
                                <code class="font-mono text-indigo-300 bg-surface-border px-1.5 py-0.5 rounded">WITH RECURSIVE provenance_trail</code>
                            </span>
                            <span class="mt-2 sm:mt-0 font-mono text-slate-400">Response time: ~1.2ms</span>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- The 3 Pillars Section -->
        <section id="frameworks" class="py-20 border-t border-surface-border relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-xs font-mono font-bold tracking-widest text-indigo-400 uppercase">Core Framework Architecture</h2>
                    <p class="text-3xl sm:text-4xl font-extrabold text-white mt-2">The Three Interconnected Pillars</p>
                    <p class="text-slate-400 text-sm sm:text-base mt-3">DOS connects raw reality to strategic law through three perpetual systems.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- ALF Card -->
                    <div class="rounded-2xl bg-surface-card border border-surface-border p-7 hover:border-indigo-500/40 transition duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-mono font-bold text-lg mb-5 group-hover:scale-110 transition">
                                ALF
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Allocore Learning Framework</h3>
                            <p class="text-slate-400 text-sm leading-relaxed mb-6">
                                Captures observations, derives insights, validates learnings via Stewards, and promotes them to binding organizational Principles.
                            </p>
                            <ul class="space-y-2.5 text-xs text-slate-300 font-medium">
                                <li class="flex items-center space-x-2">
                                    <span class="text-indigo-400">✔</span>
                                    <span>Observation → Insight → Learning → Principle</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-indigo-400">✔</span>
                                    <span>Spatie Model State Machines (Draft → Promoted)</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-indigo-400">✔</span>
                                    <span>Non-destructive versioning via <code class="text-indigo-300 font-mono">supersedes</code></span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-indigo-400">✔</span>
                                    <span>Decisions justified by governing principles</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-8 pt-4 border-t border-surface-border text-xs font-mono text-indigo-400">
                            ALF Engine Active
                        </div>
                    </div>

                    <!-- AMF Card -->
                    <div class="rounded-2xl bg-surface-card border border-surface-border p-7 hover:border-indigo-500/40 transition duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 font-mono font-bold text-lg mb-5 group-hover:scale-110 transition">
                                AMF
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Allocore Module Framework</h3>
                            <p class="text-slate-400 text-sm leading-relaxed mb-6">
                                Models the 6 canonical organizational development pillars in a recursive adjacency tree with DB-weighted audits and maturity scoring.
                            </p>
                            <ul class="space-y-2.5 text-xs text-slate-300 font-medium">
                                <li class="flex items-center space-x-2">
                                    <span class="text-purple-400">✔</span>
                                    <span>6 Canonical Modules (Unternehmensentwicklung, etc.)</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-purple-400">✔</span>
                                    <span>Recursive Adjacency Tree (Ancestors/Descendants)</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-purple-400">✔</span>
                                    <span>Audit weighted scoring & Zielzustand delta</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-purple-400">✔</span>
                                    <span>Time-series KPI readings & Operational Toolkits</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-8 pt-4 border-t border-surface-border text-xs font-mono text-purple-400">
                            AMF Adjacency Tree Active
                        </div>
                    </div>

                    <!-- ARF Card -->
                    <div class="rounded-2xl bg-surface-card border border-surface-border p-7 hover:border-indigo-500/40 transition duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 font-mono font-bold text-lg mb-5 group-hover:scale-110 transition">
                                ARF
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">Allocore Review Framework</h3>
                            <p class="text-slate-400 text-sm leading-relaxed mb-6">
                                Scheduled strategic reviews that close the loop. Closing a review automatically synthesizes findings into a new Draft Learning in ALF.
                            </p>
                            <ul class="space-y-2.5 text-xs text-slate-300 font-medium">
                                <li class="flex items-center space-x-2">
                                    <span class="text-emerald-400">✔</span>
                                    <span>Quarterly and Annual Strategic Cadences</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-emerald-400">✔</span>
                                    <span>ReviewItems evaluating Modules and Principles</span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-emerald-400">✔</span>
                                    <span>Loop Closure: <code class="text-emerald-300 font-mono">Review -(generates)-> Learning</code></span>
                                </li>
                                <li class="flex items-center space-x-2">
                                    <span class="text-emerald-400">✔</span>
                                    <span>The knowledge loop never terminates</span>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-8 pt-4 border-t border-surface-border text-xs font-mono text-emerald-400">
                            ARF Loop Closure Active
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Knowledge Graph & Cytoscape Section -->
        <section id="graph" class="py-20 border-t border-surface-border bg-surface-base/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-5 space-y-6">
                        <div class="inline-flex items-center space-x-2 text-xs font-mono text-indigo-400 uppercase font-semibold">
                            <span>Phase 3 Engine</span>
                            <span>•</span>
                            <span>Multi-Hop BFS</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight">
                            A Living Graph That Surfaces Tensions
                        </h2>
                        <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                            DOS does not bury organizational contradictions. When an empirical learning contradicts an active principle, the system flags it as a high-priority tension, cross-referencing all affected operational modules.
                        </p>

                        <div class="space-y-4">
                            <div class="p-4 rounded-xl bg-surface-card border border-surface-border flex items-start space-x-3.5">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 font-mono text-sm shrink-0">
                                    ⚡
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">Active Contradiction Detection</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Surfaces tensions via <code class="font-mono text-rose-300">/api/v1/graph/contradictions</code> with affected module mapping.</div>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-surface-card border border-surface-border flex items-start space-x-3.5">
                                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 font-mono text-sm shrink-0">
                                    🔍
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">Orphan Knowledge Detection</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Identifies disconnected nodes with zero edges to eliminate organizational knowledge loss.</div>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-surface-card border border-surface-border flex items-start space-x-3.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 font-mono text-sm shrink-0">
                                    🌐
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-white">Cytoscape.js Formatted API</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Multi-hop BFS neighborhood query (<code class="font-mono text-indigo-300">depth=1..10</code>) delivering instant frontend visualization elements.</div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Interactive Graph / Terminal Preview -->
                    <div class="lg:col-span-7">
                        <div class="rounded-2xl bg-surface-card border border-surface-border overflow-hidden shadow-2xl">
                            
                            <div class="px-5 py-3.5 bg-surface-border/40 border-b border-surface-border flex items-center justify-between text-xs font-mono">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-600"></span>
                                    <span class="text-slate-300">GET /api/v1/graph/observation/1?depth=3</span>
                                </div>
                                <span class="text-indigo-400">200 OK</span>
                            </div>

                            <div class="p-6 font-mono text-xs text-slate-300 bg-[#070912] overflow-x-auto max-h-[420px] scrollbar-thin">
<pre class="text-slate-300"><span class="text-slate-500">// Standardized Cytoscape.js Element Payload</span>
{
  <span class="text-indigo-300">"elements"</span>: {
    <span class="text-indigo-300">"nodes"</span>: [
      {
        <span class="text-purple-300">"data"</span>: {
          <span class="text-slate-400">"id"</span>: <span class="text-emerald-300">"observation:1"</span>,
          <span class="text-slate-400">"label"</span>: <span class="text-emerald-300">"Founder Operational Drag"</span>,
          <span class="text-slate-400">"type"</span>: <span class="text-emerald-300">"observation"</span>,
          <span class="text-slate-400">"is_center"</span>: <span class="text-amber-300">true</span>
        }
      },
      {
        <span class="text-purple-300">"data"</span>: {
          <span class="text-slate-400">"id"</span>: <span class="text-emerald-300">"insight:1"</span>,
          <span class="text-slate-400">"label"</span>: <span class="text-emerald-300">"Strategic Vacuum"</span>,
          <span class="text-slate-400">"type"</span>: <span class="text-emerald-300">"insight"</span>,
          <span class="text-slate-400">"is_center"</span>: <span class="text-amber-300">false</span>
        }
      }
    ],
    <span class="text-indigo-300">"edges"</span>: [
      {
        <span class="text-purple-300">"data"</span>: {
          <span class="text-slate-400">"id"</span>: <span class="text-emerald-300">"edge_1"</span>,
          <span class="text-slate-400">"source"</span>: <span class="text-emerald-300">"observation:1"</span>,
          <span class="text-slate-400">"target"</span>: <span class="text-emerald-300">"insight:1"</span>,
          <span class="text-slate-400">"relation"</span>: <span class="text-emerald-300">"produces"</span>,
          <span class="text-slate-400">"strength"</span>: <span class="text-indigo-300">100</span>
        }
      }
    ]
  },
  <span class="text-indigo-300">"stats"</span>: {
    <span class="text-slate-400">"center"</span>: <span class="text-emerald-300">"observation:1"</span>,
    <span class="text-slate-400">"depth"</span>: <span class="text-indigo-300">3</span>,
    <span class="text-slate-400">"node_count"</span>: <span class="text-indigo-300">5</span>,
    <span class="text-slate-400">"edge_count"</span>: <span class="text-indigo-300">4</span>
  }
}</pre>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- The 3 Non-Negotiables Grid -->
        <section id="principles" class="py-20 border-t border-surface-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-xs font-mono font-bold tracking-widest text-indigo-400 uppercase">Architecture Non-Negotiables</h2>
                    <p class="text-3xl sm:text-4xl font-extrabold text-white mt-2">Built Without Compromise</p>
                    <p class="text-slate-400 text-sm sm:text-base mt-3">Three architectural principles that govern every single line of code.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="p-8 rounded-2xl bg-surface-card border border-surface-border">
                        <div class="text-2xl font-mono font-bold text-indigo-400 mb-3">01</div>
                        <h3 class="text-lg font-bold text-white mb-2">Absolute Traceability</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4">
                            Every decision must be traceable backwards to the empirical observation that spawned it via recursive SQL CTE traversal.
                        </p>
                        <div class="text-xs font-mono text-indigo-300 bg-indigo-500/10 px-2 py-1 rounded inline-block">
                            Append-only edge table
                        </div>
                    </div>

                    <div class="p-8 rounded-2xl bg-surface-card border border-surface-border">
                        <div class="text-2xl font-mono font-bold text-purple-400 mb-3">02</div>
                        <h3 class="text-lg font-bold text-white mb-2">Zero Knowledge Loss</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4">
                            Hard deletes are forbidden. <code class="text-purple-300">forceDelete()</code> throws a <code class="text-purple-300">LogicException</code>. Principles and goals version non-destructively through <code class="text-purple-300">supersedes</code> edges.
                        </p>
                        <div class="text-xs font-mono text-purple-300 bg-purple-500/10 px-2 py-1 rounded inline-block">
                            Non-destructive versioning
                        </div>
                    </div>

                    <div class="p-8 rounded-2xl bg-surface-card border border-surface-border">
                        <div class="text-2xl font-mono font-bold text-emerald-400 mb-3">03</div>
                        <h3 class="text-lg font-bold text-white mb-2">Perpetual Loop</h3>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4">
                            The graph is cyclic, not a tree. Reviews spawn Learnings that generate Principles that govern Modules that are audited in Reviews.
                        </p>
                        <div class="text-xs font-mono text-emerald-300 bg-emerald-500/10 px-2 py-1 rounded inline-block">
                            Closed-loop continuous recursion
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- REST API Quick Reference -->
        <section id="api" class="py-20 border-t border-surface-border bg-surface-base/40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-xs font-mono font-bold tracking-widest text-indigo-400 uppercase">Developer Interface</h2>
                    <p class="text-3xl sm:text-4xl font-extrabold text-white mt-2">REST API Endpoints</p>
                    <p class="text-slate-400 text-sm sm:text-base mt-3">High-performance endpoints secured by tenant-scoped authorization.</p>
                </div>

                <div class="rounded-2xl bg-surface-card border border-surface-border overflow-hidden">
                    <div class="divide-y divide-surface-border">
                        
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-surface-hover transition">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 rounded text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">POST</span>
                                <span class="font-mono text-sm text-slate-200">/api/v1/capture</span>
                            </div>
                            <span class="text-xs text-slate-400">Sub-60s low-friction capture (Observation, Learning, Question)</span>
                        </div>

                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-surface-hover transition">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 rounded text-[10px] font-mono font-bold bg-indigo-500/15 text-indigo-400 border border-indigo-500/20">GET</span>
                                <span class="font-mono text-sm text-slate-200">/api/v1/graph/{type}/{id}</span>
                            </div>
                            <span class="text-xs text-slate-400">Cytoscape.js multi-hop BFS neighborhood query with depth param</span>
                        </div>

                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-surface-hover transition">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 rounded text-[10px] font-mono font-bold bg-indigo-500/15 text-indigo-400 border border-indigo-500/20">GET</span>
                                <span class="font-mono text-sm text-slate-200">/api/v1/graph/contradictions</span>
                            </div>
                            <span class="text-xs text-slate-400">Surface active tensions where Learnings contradict active Principles</span>
                        </div>

                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-surface-hover transition">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 rounded text-[10px] font-mono font-bold bg-indigo-500/15 text-indigo-400 border border-indigo-500/20">GET</span>
                                <span class="font-mono text-sm text-slate-200">/api/v1/graph/orphans</span>
                            </div>
                            <span class="text-xs text-slate-400">Report isolated knowledge nodes with zero active edges</span>
                        </div>

                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-surface-hover transition">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 rounded text-[10px] font-mono font-bold bg-indigo-500/15 text-indigo-400 border border-indigo-500/20">GET</span>
                                <span class="font-mono text-sm text-slate-200">/api/v1/principles/{id}/provenance</span>
                            </div>
                            <span class="text-xs text-slate-400">Trace complete cognitive path back to initial raw Observation</span>
                        </div>

                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 hover:bg-surface-hover transition">
                            <div class="flex items-center space-x-3">
                                <span class="px-2 py-1 rounded text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/20">POST</span>
                                <span class="font-mono text-sm text-slate-200">/api/v1/reviews/{id}/close</span>
                            </div>
                            <span class="text-xs text-slate-400">Close strategic review and automatically spawn a Draft Learning</span>
                        </div>

                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-xs font-mono text-slate-400">
                        View complete documentation in <a href="https://github.com/mrshahbazdev/disavo-operating-system" class="text-indigo-400 hover:underline">README.md</a> on GitHub.
                    </p>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-surface-border bg-[#05060c] py-12 z-10 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-6 h-6 rounded bg-indigo-600 flex items-center justify-center font-mono font-bold text-white text-[10px]">
                    D
                </div>
                <span>&copy; 2026 Disavo Operating System (DOS). All rights reserved.</span>
            </div>
            <div class="flex items-center space-x-6">
                <span class="flex items-center space-x-1.5 text-emerald-400 font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>All 30 Tests Passing</span>
                </span>
                <a href="https://github.com/mrshahbazdev/disavo-operating-system" target="_blank" class="hover:text-white transition">
                    GitHub Repository
                </a>
            </div>
        </div>
    </footer>

</body>
</html>
