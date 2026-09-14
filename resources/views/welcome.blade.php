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
                        corporate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            500: '#2563eb',
                            600: '#1d4ed8',
                        },
                        dark: {
                            base: '#0c0e14',
                            surface: '#131620',
                            card: '#161a26',
                            border: '#1e2333',
                            hover: '#1a1f2e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0c0e14; }
    </style>
</head>
<body class="bg-[#0c0e14] text-slate-200 min-h-screen flex flex-col antialiased">

    <!-- Top Header -->
    <header class="border-b border-dark-border bg-dark-surface/90 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm font-mono shadow">
                    D
                </div>
                <div>
                    <span class="font-bold text-sm tracking-tight text-white block">DOS</span>
                    <span class="text-[11px] text-slate-400 block -mt-0.5 font-medium">Disavo Operating System</span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <span class="text-xs text-slate-400 hidden sm:inline">Disavo Holding GmbH</span>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition">
                        Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold rounded-lg bg-blue-600 hover:bg-blue-500 text-white transition">
                        Sign In
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
                            Organizational Knowledge Graph & Development System
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

                    <!-- Right Column: Direct Sign-In Card or Live System Status -->
                    <div class="lg:col-span-5">
                        <div class="bg-dark-surface border border-dark-border rounded-2xl p-6 sm:p-8 shadow-xl">
                            
                            @auth
                                <div class="text-center py-6 space-y-4">
                                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center mx-auto text-xl">
                                        ✓
                                    </div>
                                    <h3 class="text-lg font-bold text-white">Authenticated Session Active</h3>
                                    <p class="text-xs text-slate-400">
                                        Logged in as <span class="text-white font-medium">{{ auth()->user()->name }}</span> ({{ auth()->user()->email }}).
                                    </p>
                                    <div class="pt-2">
                                        <a href="{{ route('dashboard') }}" class="w-full block py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition text-center">
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

        <!-- The 3 Pillars Section -->
        <section id="frameworks" class="py-16 sm:py-20 border-b border-dark-border">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-3xl mb-12">
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase tracking-wider">System Architecture</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">The Three Interlocking Frameworks</h2>
                    <p class="text-slate-400 text-sm mt-2">DOS operates as a closed continuous learning loop connecting modules to strategic principles.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- ALF -->
                    <div class="p-6 rounded-xl bg-dark-surface border border-dark-border">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 text-blue-400 font-mono font-bold flex items-center justify-center text-sm mb-4">
                            ALF
                        </div>
                        <h3 class="text-base font-bold text-white">Allocore Learning Framework</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Complete cognitive traceability: Observations yield Insights, which validate Learnings, which promote into binding Principles governing corporate actions.
                        </p>
                        <div class="mt-4 pt-3 border-t border-dark-border/60 text-[11px] font-mono text-slate-400">
                            Strict Spatie state machine & non-destructive versioning.
                        </div>
                    </div>

                    <!-- AMF -->
                    <div class="p-6 rounded-xl bg-dark-surface border border-dark-border">
                        <div class="w-10 h-10 rounded-lg bg-slate-500/10 text-slate-300 font-mono font-bold flex items-center justify-center text-sm mb-4">
                            AMF
                        </div>
                        <h3 class="text-base font-bold text-white">Allocore Module Framework</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Organizes corporate development into an adjacency tree across 6 canonical pillars with database-weighted audits and maturity delta scoring against target states.
                        </p>
                        <div class="mt-4 pt-3 border-t border-dark-border/60 text-[11px] font-mono text-slate-400">
                            Recursive tree traversal & KPI time-series readings.
                        </div>
                    </div>

                    <!-- ARF -->
                    <div class="p-6 rounded-xl bg-dark-surface border border-dark-border">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 font-mono font-bold flex items-center justify-center text-sm mb-4">
                            ARF
                        </div>
                        <h3 class="text-base font-bold text-white">Allocore Review Framework</h3>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Strategic reviews that evaluate modules and principles. Closing a review automatically spawns a new Draft Learning in ALF, ensuring the learning loop never ends.
                        </p>
                        <div class="mt-4 pt-3 border-t border-dark-border/60 text-[11px] font-mono text-slate-400">
                            Continuous loop closure: Review &rarr; Learning.
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- The 6 Canonical Modules Overview -->
        <section class="py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-3xl mb-12">
                    <span class="text-xs font-mono font-bold text-blue-400 uppercase tracking-wider">Corporate Development</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mt-1">Six Canonical Pillars</h2>
                    <p class="text-slate-400 text-sm mt-2">The structural foundation governing Disavo Holding GmbH.</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-dark-surface border border-dark-border">
                        <span class="text-[10px] font-mono text-slate-500 block">MODULE 01</span>
                        <h4 class="text-sm font-bold text-white mt-1">Unternehmensentwicklung</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Strategic enterprise growth and structuring.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-dark-surface border border-dark-border">
                        <span class="text-[10px] font-mono text-slate-500 block">MODULE 02</span>
                        <h4 class="text-sm font-bold text-white mt-1">Markenaufbau</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Brand equity, positioning, and market presence.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-dark-surface border border-dark-border">
                        <span class="text-[10px] font-mono text-slate-500 block">MODULE 03</span>
                        <h4 class="text-sm font-bold text-white mt-1">Nachfolge</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Succession planning and generational continuity.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-dark-surface border border-dark-border">
                        <span class="text-[10px] font-mono text-slate-500 block">MODULE 04</span>
                        <h4 class="text-sm font-bold text-white mt-1">Unternehmerentwicklung</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Leadership delegation and executive capacity.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-dark-surface border border-dark-border">
                        <span class="text-[10px] font-mono text-slate-500 block">MODULE 05</span>
                        <h4 class="text-sm font-bold text-white mt-1">Beteiligungsmanagement</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Portfolio governance and equity steering.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-dark-surface border border-dark-border">
                        <span class="text-[10px] font-mono text-slate-500 block">MODULE 06</span>
                        <h4 class="text-sm font-bold text-white mt-1">Kapitalallokation</h4>
                        <p class="text-[11px] text-slate-400 mt-1">Capital distribution, reinvestment, and treasury.</p>
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

</body>
</html>
