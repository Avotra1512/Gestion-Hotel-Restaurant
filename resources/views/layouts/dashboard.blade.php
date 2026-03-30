<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MISALO - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ← AJOUTER CES STYLES --}}
    <style>
        /* Scrollbar invisible sur le sidebar */
        nav.sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        nav.sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        nav.sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 99px;
        }
        nav.sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.3);
        }
        /* Firefox */
        nav.sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.08) transparent;
        }
    </style>
</head>
<body class="bg-black text-white font-sans antialiased">

<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

    {{-- ══════════════════════════════════════════════════════════
         OVERLAY MOBILE (fond sombre derrière le sidebar)
         ══════════════════════════════════════════════════════════ --}}
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/70 backdrop-blur-sm z-30 lg:hidden"
         style="display:none">
    </div>

    {{-- ══════════════════════════════════════════════════════════
         SIDEBAR — fixe, ne scroll pas avec la page
         ══════════════════════════════════════════════════════════ --}}
    <aside class="fixed top-0 left-0 h-screen w-64 bg-zinc-950 border-r border-white/5
                  flex flex-col z-40
                  transform transition-transform duration-300 ease-in-out
                  -translate-x-full lg:translate-x-0"
           :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
           x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="px-8 py-6 border-b border-white/5 flex-shrink-0">
            <h2 class="text-2xl font-bold tracking-widest text-amber-400">MISALO</h2>
            <p class="text-[10px] text-white/40 tracking-widest uppercase mt-1">Espace Privé</p>
        </div>

        {{-- Navigation — scrollable si trop d'items --}}
        <nav class="sidebar-nav flex-1 px-4 py-4 overflow-y-auto space-y-1">
            @yield('sidebar-links-nav')
        </nav>

        {{-- Bouton déconnexion — FIXE en bas du sidebar --}}
        <div class="flex-shrink-0 px-4 py-4 border-t border-white/5">
            @yield('sidebar-logout')
        </div>

    </aside>

    {{-- ══════════════════════════════════════════════════════════
         CONTENU PRINCIPAL
         ══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">

        {{-- HEADER fixe en haut --}}
        <header class="sticky top-0 z-20 h-16 bg-zinc-950/95 backdrop-blur-sm
                       border-b border-white/5 flex items-center justify-between px-6 flex-shrink-0">

            {{-- Bouton hamburger (mobile uniquement) --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden w-10 h-10 flex items-center justify-center
                           rounded-xl border border-white/10 text-white/60
                           hover:border-amber-400/50 hover:text-amber-400 transition">
                <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Titre de la page --}}
            <h1 class="text-base font-semibold text-white/80 hidden sm:block">
                @yield('title')
            </h1>

            {{-- Droite : cloche + user --}}
            <div class="flex items-center gap-3 ml-auto">

                {{-- Cloche notifications --}}
                @include('components.notification-bell')
                
                {{-- Avatar utilisateur --}}
                <div class="flex items-center gap-2">
                    <span class="text-sm text-white/50 hidden md:block">
                        {{ Auth::user()->name ?? 'Utilisateur' }}
                    </span>
                    <div class="w-9 h-9 rounded-full bg-amber-400/20 border border-amber-400/40
                                flex items-center justify-center text-amber-400 font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENU de la page --}}
        <main class="flex-1 overflow-y-auto">
            <div class="p-6 lg:p-10">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('styles')
@stack('scripts')
</body>
</html>
