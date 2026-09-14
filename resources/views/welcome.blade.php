<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Disavo Operating System (DOS) — Disavo Holding GmbH</title>

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

    <!-- Top Header -->
    <header class="border-b border-dark-border bg-dark-surface/90 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm font-mono shadow hover:bg-blue-500 transition">
                    D
                </a>
                <div>
                    <span class="font-bold text-sm tracking-tight text-white block">DOS</span>
                    <span class="text-[11px] text-slate-400 block -mt-0.5 font-medium">Disavo Operating System</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-xs text-slate-400 hidden sm:inline">Disavo Holding GmbH</span>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition">
                        Open Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition shadow-sm">
                        Create Account
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        
        <!-- Hero Section -->
        <section class="py-16 sm:py-24 border-b border-dark-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    
                    <div class="lg:col-span-7 space-y-6">
                        <div class="inline-block px-3 py-1 rounded bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold tracking-wide">
                            DISAVO HOLDING GMBH • ENTERPRISE CORE
                        </div>

                        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                            Organizational Knowledge Graph &amp; Development System
                        </h1>

                        <p class="text-slate-400 text-base sm:text-lg leading-relaxed max-w-xl">
                            A cyclic, traceable, append-only knowledge architecture connecting corporate development modules, empirical learnings, governing principles, and strategic reviews.
                        </p>

                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            @auth
                                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition">
                                    Open Executive Dashboard &rarr;
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition">
                                    Sign In to DOS
                                </a>
                            @endauth
                            <a href="#frameworks" class="px-6 py-3 rounded-xl bg-dark-surface border border-dark-border hover:bg-dark-hover text-slate-300 hover:text-white font-semibold text-sm transition">
                                System Architecture
                            </a>
                        </div>
                    </div>

                    <!-- Right Column: Direct Sign-In Card or Authenticated Status -->
                    <div class="lg:col-span-5">
                        <div class="bg-dark-surface border border-dark-border rounded-2xl p-6 sm:p-8 shadow-xl">
                            
                            @auth
                                <div class="text-center py-6 space-y-4">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto text-xl font-bold">
                                        ✓
                                    </div>
                                    <h3 class="text-lg font-bold text-white">Authenticated Session Active</h3>
                                    <p class="text-xs text-slate-400">
                                        Logged in as <span class="text-white font-medium">{{ auth()->user()->name }}</span> ({{ auth()->user()->email }}).
                                    </p>
                                    <div class="pt-2">
                                        <a href="{{ route('dashboard') }}" class="w-full block py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition text-center shadow">
                                            Go to Dashboard
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="mb-5">
                                    <h2 class="text-lg font-bold text-white tracking-tight">Executive Sign In</h2>
                                    <p class="text-xs text-slate-400 mt-0.5">Authenticate to enter Disavo Operating System.</p>
                                </div>

                                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                                    @csrf

                                    <div>
                                        <label for="hero-email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                                            Email Address
                                        </label>
                                        <input 
                                            type="email" 
                                            name="email" 
                                            id="hero-email" 
                                            required 
                                            value="owner@disavo.de"
                                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#090b10] border border-dark-border text-white text-sm focus:outline-none focus:border-blue-500 transition"
                                        >
                                    </div>

                                    <div>
                                        <label for="hero-password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                                            Password
                                        </label>
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="hero-password" 
                                            required 
                                            value="secret123"
                                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#090b10] border border-dark-border text-white text-sm focus:outline-none focus:border-blue-500 transition font-mono"
                                        >
                                    </div>

                                    <button 
                                        type="submit" 
                                        class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition shadow mt-2"
                                    >
                                        Sign In to Portal
                                    </button>

                                    <div class="text-center pt-2">
                                        <span class="text-xs text-slate-400">Need a new account?</span>
                                        <a href="{{ route('register') }}" class="text-xs font-semibold text-blue-400 hover:text-blue-300 ml-1 underline underline-offset-2">
                                            Register Organization &rarr;
                                        </a>
                                    </div>
                                </form>

                                <div class="mt-4 pt-4 border-t border-dark-border text-[11px] text-slate-400 flex items-center justify-between">
                                    <span>Default: <code class="text-slate-300 font-mono">owner@disavo.de</code></span>
                                    <span>PW: <code class="text-slate-300 font-mono">secret123</code></span>
                                </div>
                            @endauth

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- The 3 Pillars Section (Clickable) -->
        <section id="frameworks" class="py-16 sm:py-20 border-b border-dark-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-3xl mb-12">
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase tracking-wider">System Architecture</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">The Three Interlocking Frameworks</h2>
                    <p class="text-slate-400 text-sm mt-2">Click any framework card below to inspect its architecture, state machine, and data flow.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- ALF Card -->
                    <div 
                        onclick="openFrameworkInfo('alf')"
                        class="p-6 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-400 font-mono font-bold flex items-center justify-center text-sm">
                                    ALF
                                </div>
                                <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                            </div>
                            <h3 class="text-base font-bold text-white group-hover:text-blue-400 transition">Allocore Learning Framework</h3>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                Complete cognitive traceability: Observations yield Insights, which validate Learnings, which promote into binding Principles governing corporate actions.
                            </p>
                            <div class="mt-4 pt-3 border-t border-dark-border/60 text-[11px] font-mono text-slate-400">
                                Strict Spatie state machine &amp; non-destructive versioning.
                            </div>
                        </div>
                        <div class="mt-4 pt-2 text-xs text-blue-400 font-medium">
                            Click to inspect cognitive trail &rarr;
                        </div>
                    </div>

                    <!-- AMF Card -->
                    <div 
                        onclick="openFrameworkInfo('amf')"
                        class="p-6 rounded-xl bg-dark-surface border border-dark-border hover:border-purple-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-lg bg-purple-500/10 text-purple-400 font-mono font-bold flex items-center justify-center text-sm">
                                    AMF
                                </div>
                                <span class="text-xs text-purple-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                            </div>
                            <h3 class="text-base font-bold text-white group-hover:text-purple-400 transition">AMF 1.1 — Allocore Module Framework</h3>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                Die Modulfabrik von Allocore: Blaupause mit 9 Standard-Bausteinen über 5 Allocore-Ebenen (Umsatz, Gewinn, Ordnung, Einfluss, Vermächtnis). Prinzip AP-005: Ein Modul ist kein Audit, sondern ein ganzheitliches Führungssystem.
                            </p>
                            <div class="mt-4 pt-3 border-t border-dark-border/60 text-[11px] font-mono text-slate-400">
                                Blaupause für Anwendungsmodule (Marke, Unternehmer, Führung, Nachfolge).
                            </div>
                        </div>
                        <div class="mt-4 pt-2 text-xs text-purple-400 font-medium">
                            AMF-Blaupause &amp; 9 Bausteine einsehen &rarr;
                        </div>
                    </div>

                    <!-- ARF Card -->
                    <div 
                        onclick="openFrameworkInfo('arf')"
                        class="p-6 rounded-xl bg-dark-surface border border-dark-border hover:border-emerald-500 hover:bg-dark-hover transition duration-200 cursor-pointer group flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 font-mono font-bold flex items-center justify-center text-sm">
                                    ARF
                                </div>
                                <span class="text-xs text-emerald-400 font-semibold group-hover:translate-x-1 transition">&rarr;</span>
                            </div>
                            <h3 class="text-base font-bold text-white group-hover:text-emerald-400 transition">Allocore Review Framework</h3>
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                Strategic reviews that evaluate modules and principles. Closing a review automatically spawns a new Draft Learning in ALF, ensuring the learning loop never ends.
                            </p>
                            <div class="mt-4 pt-3 border-t border-dark-border/60 text-[11px] font-mono text-slate-400">
                                Continuous loop closure: Review &rarr; Learning.
                            </div>
                        </div>
                        <div class="mt-4 pt-2 text-xs text-emerald-400 font-medium">
                            Click to inspect review loops &rarr;
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- The 6 Canonical Modules Overview (Clickable) -->
        <section class="py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-3xl mb-12">
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase tracking-wider">Corporate Development</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">Six Canonical Pillars</h2>
                    <p class="text-slate-400 text-sm mt-2">Click any module card to inspect its scope, target state, and operational function.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div 
                        onclick="openModuleInfo('unternehmensentwicklung')"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 block">MODULE 01</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white mt-1 group-hover:text-blue-400 transition">Unternehmensentwicklung</h4>
                        <span class="text-xs font-semibold text-blue-400 block mt-0.5">Company Development (Enterprise &amp; Scaling)</span>
                        <p class="text-xs text-slate-400 mt-1.5">Strategic enterprise growth, corporate scaling, and organizational structuring.</p>
                    </div>

                    <div 
                        onclick="openModuleInfo('markenaufbau')"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 block">MODULE 02</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white mt-1 group-hover:text-blue-400 transition">Markenaufbau</h4>
                        <span class="text-xs font-semibold text-blue-400 block mt-0.5">Brand Building (Positioning &amp; Authority)</span>
                        <p class="text-xs text-slate-400 mt-1.5">Brand equity, positioning, market authority, and competitive differentiation.</p>
                    </div>

                    <div 
                        onclick="openModuleInfo('nachfolge')"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 block">MODULE 03</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white mt-1 group-hover:text-blue-400 transition">Nachfolge</h4>
                        <span class="text-xs font-semibold text-blue-400 block mt-0.5">Succession (Generational Handover)</span>
                        <p class="text-xs text-slate-400 mt-1.5">Succession planning, generational handover, and continuity governance.</p>
                    </div>

                    <div 
                        onclick="openModuleInfo('unternehmerentwicklung')"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 block">MODULE 04</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white mt-1 group-hover:text-blue-400 transition">Unternehmerentwicklung</h4>
                        <span class="text-xs font-semibold text-purple-400 block mt-0.5">Entrepreneur Development (Founder &amp; Leadership)</span>
                        <p class="text-xs text-slate-400 mt-1.5">Leadership delegation, executive capacity, and founder role transition.</p>
                    </div>

                    <div 
                        onclick="openModuleInfo('beteiligungsmanagement')"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 block">MODULE 05</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white mt-1 group-hover:text-blue-400 transition">Beteiligungsmanagement</h4>
                        <span class="text-xs font-semibold text-blue-400 block mt-0.5">Investment Management (Portfolio Steering)</span>
                        <p class="text-xs text-slate-400 mt-1.5">Holding portfolio governance, equity steering, and group-level synergies.</p>
                    </div>

                    <div 
                        onclick="openModuleInfo('kapitalallokation')"
                        class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500 hover:bg-dark-hover transition duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-mono text-slate-500 block">MODULE 06</span>
                            <span class="text-xs text-blue-400 font-semibold group-hover:translate-x-0.5 transition">&rarr;</span>
                        </div>
                        <h4 class="text-sm font-bold text-white mt-1 group-hover:text-blue-400 transition">Kapitalallokation</h4>
                        <span class="text-xs font-semibold text-blue-400 block mt-0.5">Capital Allocation (Reinvestment &amp; Liquidity)</span>
                        <p class="text-xs text-slate-400 mt-1.5">Capital distribution, reinvestment strategy, liquidity, and yield optimization.</p>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-dark-border py-6 px-4 sm:px-8 bg-dark-surface">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div>
                &copy; 2026 Disavo Holding GmbH • Disavo Operating System (DOS)
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition">Sign In</a>
                <span>•</span>
                <span>Authorized Personnel Only</span>
            </div>
        </div>
    </footer>

    <!-- Informational Modal for Public Page -->
    <div id="infoModal" class="fixed inset-0 z-50 hidden bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-dark-surface border border-dark-border rounded-2xl max-w-xl w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-dark-border flex items-start justify-between">
                <div>
                    <span id="infoCategory" class="text-xs font-mono font-bold text-blue-400 uppercase">Architecture</span>
                    <h3 id="infoTitle" class="text-xl font-bold text-white mt-1">Title</h3>
                </div>
                <button onclick="closeModal('infoModal')" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-dark-hover transition">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-4 text-sm text-slate-300">
                <p id="infoContent" class="text-xs text-slate-300 leading-relaxed"></p>

                <div id="infoMeta" class="p-3.5 rounded-xl bg-[#090b10] border border-dark-border text-xs">
                    <!-- Additional details filled dynamically -->
                </div>
            </div>

            <div class="p-4 border-t border-dark-border bg-[#090b10] flex items-center justify-between">
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold transition">
                    Sign In to Access System &rarr;
                </a>
                <button onclick="closeModal('infoModal')" class="px-4 py-2 rounded-xl bg-dark-hover hover:bg-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Client-Side Interactive Script -->
    <script>
        const MODULE_DETAILS = {
            'unternehmensentwicklung': {
                title: 'Unternehmensentwicklung (Corporate Development)',
                category: 'Canonical Module 01',
                desc: 'Focuses on strategic enterprise growth, organizational structuring, process standardisation, and scalable governance architectures.',
                meta: 'Includes Zielzustand maturity evaluation, weighted organizational audits, and structural toolkits.'
            },
            'markenaufbau': {
                title: 'Markenaufbau (Brand Building)',
                category: 'Canonical Module 02',
                desc: 'Governs market positioning, brand authority, competitive differentiation, and corporate reputation management.',
                meta: 'Tracks brand equity KPIs and structural brand guidelines across the holding group.'
            },
            'nachfolge': {
                title: 'Nachfolge (Succession Planning)',
                category: 'Canonical Module 03',
                desc: 'Addresses generational transition, advisory board establishment, leadership handover processes, and continuity preservation.',
                meta: 'Governed by canonical succession principles ensuring business survival beyond individual founders.'
            },
            'unternehmerentwicklung': {
                title: 'Unternehmerentwicklung (Founder Development)',
                category: 'Canonical Module 04',
                desc: 'Focuses on executive role reflection, leadership delegation, and transitioning the founder from operational firefighting to strategic portfolio steering.',
                meta: 'Audited against the Principle of Subsidiarity (no decision made higher than necessary).'
            },
            'beteiligungsmanagement': {
                title: 'Beteiligungsmanagement (Portfolio Management)',
                category: 'Canonical Module 05',
                desc: 'Manages multi-company asset allocation, group-level synergies, subsidiary governance, and portfolio value enhancement programs.',
                meta: 'Monitors subsidiary maturity scores and strategic review items across all holding participations.'
            },
            'kapitalallokation': {
                title: 'Kapitalallokation (Capital Allocation)',
                category: 'Canonical Module 06',
                desc: 'Directs reinvestment strategy, liquidity management, dividend policies, and high-return treasury optimization.',
                meta: 'All major capital deployments require verified decision paths linked to active principles.'
            }
        };

        const FRAMEWORK_DETAILS = {
            'alf': {
                title: 'ALF — Allocore Learning Framework',
                category: 'System Pillar 01',
                desc: 'Provides complete cognitive traceability. Every strategic decision must be traceable backwards: Observation ➔ Insight ➔ Validated Learning ➔ Governing Principle ➔ Decision.',
                meta: 'Powered by strict Spatie model state machines and non-destructive principle supersession.'
            },
            'amf': {
                title: 'AMF 1.1 — Allocore Module Framework: Die Modulfabrik',
                category: 'System-Blaupause & Modulfabrik',
                desc: 'Bevor Module entstehen, definiert das AMF, wie Module aufgebaut sind: 1. Entwicklungsobjekt, 2. messbare Zieldefinition, 3. die 5 Allocore-Ebenen (Umsatz, Gewinn, Ordnung, Einfluss, Vermächtnis), 4. Audit, 5. KPI-System, 6. Werkzeuge, 7. Learnings (ALF), 8. Review-Zyklus (ARF) und 9. Versionierung. Prinzip AP-005: Ein Modul ist kein Audit.',
                meta: 'Blaupause für alle Anwendungsmodule wie Markenaufbau, Unternehmerentwicklung, Führung, Nachfolge.'
            },
            'arf': {
                title: 'ARF — Allocore Review Framework',
                category: 'System Pillar 03',
                desc: 'Drives the continuous cyclic loop. When a strategic review is closed, DOS executes SpawnLearningFromReview, automatically creating a new Draft Learning in ALF.',
                meta: 'Guarantees the knowledge loop never terminates: Review ➔ Learning ➔ Principle ➔ Module ➔ Audit ➔ Review.'
            }
        };

        function openModuleInfo(slug) {
            const data = MODULE_DETAILS[slug];
            if (!data) return;
            document.getElementById('infoCategory').innerText = data.category;
            document.getElementById('infoTitle').innerText = data.title;
            document.getElementById('infoContent').innerText = data.desc;
            document.getElementById('infoMeta').innerText = data.meta;
            openModal('infoModal');
        }

        function openFrameworkInfo(key) {
            const data = FRAMEWORK_DETAILS[key];
            if (!data) return;
            document.getElementById('infoCategory').innerText = data.category;
            document.getElementById('infoTitle').innerText = data.title;
            document.getElementById('infoContent').innerText = data.desc;
            document.getElementById('infoMeta').innerText = data.meta;
            openModal('infoModal');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.body.classList.remove('modal-open');
        }

        window.onclick = function(e) {
            const modal = document.getElementById('infoModal');
            if (e.target === modal) closeModal('infoModal');
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal('infoModal');
        });
    </script>

</body>
</html>
