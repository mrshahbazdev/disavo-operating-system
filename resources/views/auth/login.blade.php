<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Disavo Operating System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

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
                        brand: {
                            50: '#f0f4ff',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        dark: {
                            base: '#0c0e14',
                            surface: '#131620',
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
<body class="bg-[#0c0e14] text-slate-100 min-h-screen flex flex-col justify-between antialiased">

    <!-- Header -->
    <header class="border-b border-dark-border py-4 px-6 sm:px-12 flex items-center justify-between">
        <a href="/" class="flex items-center space-x-3 group">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-white text-sm font-mono shadow">
                D
            </div>
            <div>
                <span class="font-bold text-base tracking-tight text-white block">DOS</span>
                <span class="text-[11px] text-slate-400 block -mt-1 font-medium">Disavo Operating System</span>
            </div>
        </a>

        <a href="/" class="text-xs text-slate-400 hover:text-white transition flex items-center space-x-1">
            <span>&larr; Back to Portal</span>
        </a>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <!-- Card -->
            <div class="bg-dark-surface border border-dark-border rounded-2xl p-8 shadow-xl">
                
                <div class="mb-6">
                    <div class="inline-block px-2.5 py-1 rounded bg-blue-500/10 text-blue-400 text-[11px] font-semibold mb-3">
                        DISAVO HOLDING GMBH
                    </div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">System Sign In</h1>
                    <p class="text-xs text-slate-400 mt-1">Authenticate to access organizational knowledge graph & modules.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Work Email
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            required 
                            autofocus
                            value="{{ old('email', 'owner@disavo.de') }}"
                            placeholder="name@disavo.de"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#090b10] border border-dark-border text-white text-sm focus:outline-none focus:border-blue-500 transition"
                        >
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">
                                Password
                            </label>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            required 
                            value="secret123"
                            placeholder="••••••••••••"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-[#090b10] border border-dark-border text-white text-sm focus:outline-none focus:border-blue-500 transition font-mono"
                        >
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2 text-xs text-slate-400 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded bg-[#090b10] border-dark-border text-blue-600 focus:ring-0">
                            <span>Keep me signed in</span>
                        </label>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm transition shadow-lg shadow-blue-600/20 mt-2"
                    >
                        Sign In to DOS
                    </button>
                </form>

                <!-- Demo Credentials Helper -->
                <div class="mt-6 pt-5 border-t border-dark-border">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block mb-2">Default Accounts (Seed Pack):</span>
                    <div class="space-y-1.5 text-xs font-mono text-slate-300">
                        <div class="flex justify-between items-center p-2 rounded-lg bg-[#090b10] border border-dark-border/60">
                            <span>owner@disavo.de</span>
                            <span class="text-blue-400 text-[11px]">Owner / secret123</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded-lg bg-[#090b10] border border-dark-border/60">
                            <span>steward@disavo.de</span>
                            <span class="text-slate-400 text-[11px]">Steward / secret123</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-dark-border py-4 px-6 text-center text-xs text-slate-500">
        &copy; 2026 Disavo Holding GmbH • Disavo Operating System
    </footer>

</body>
</html>
