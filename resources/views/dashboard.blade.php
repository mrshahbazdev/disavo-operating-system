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
    </style>
</head>
<body class="bg-[#0c0e14] text-slate-200 min-h-screen flex flex-col antialiased">

    <!-- Top Navigation Bar -->
    <header class="border-b border-dark-border bg-dark-surface/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm font-mono shadow">
                    D
                </div>
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
                    OPERATIONAL CORE
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Organizational Intelligence Portal</h1>
                <p class="text-xs text-slate-400 mt-1">Disavo Operating System • Cyclic Knowledge Graph & Frameworks</p>
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
                    <p class="text-xs text-slate-400">AMF Organizational Tree</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($modules as $module)
                    <div class="p-5 rounded-xl bg-dark-surface border border-dark-border hover:border-blue-500/40 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 uppercase">Module {{ $loop->iteration }}</span>
                                <h3 class="text-base font-bold text-white mt-0.5">{{ $module->name }}</h3>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                Active
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-2 line-clamp-2">
                            {{ $module->description ?? 'Primary pillar of Disavo Holding corporate development.' }}
                        </p>
                        <div class="mt-4 pt-3 border-t border-dark-border/60 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Slug: <code class="font-mono text-slate-300">{{ $module->slug }}</code></span>
                            <span class="text-blue-400 hover:underline cursor-pointer">Inspect &rarr;</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3 Pillars Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="p-6 rounded-2xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-mono font-bold text-blue-400 uppercase">ALF Engine</div>
                <h3 class="text-base font-bold text-white mt-1">Cognitive Traceability</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Decisions are verified backwards to empirical Observations. Principles update non-destructively through versioned Supersedes relationships.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-mono font-bold text-purple-400 uppercase">AMF Engine</div>
                <h3 class="text-base font-bold text-white mt-1">Audit & Maturity Delta</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Audit questions use database-configured weights to evaluate actual maturity against target state (Zielzustand).
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-dark-surface border border-dark-border">
                <div class="text-xs font-mono font-bold text-emerald-400 uppercase">ARF Engine</div>
                <h3 class="text-base font-bold text-white mt-1">Loop Closure</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Closing a Review triggers SpawnLearningFromReview, creating a Draft Learning in ALF to ensure continuous organizational evolution.
                </p>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-dark-border py-4 px-6 text-center text-xs text-slate-500">
        &copy; 2026 Disavo Holding GmbH • Disavo Operating System (DOS)
    </footer>

</body>
</html>
