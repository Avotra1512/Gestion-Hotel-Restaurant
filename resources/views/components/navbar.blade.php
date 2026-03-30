{{-- resources/views/components/navbar.blade.php --}}
<header class="fixed top-0 left-0 w-full z-50 bg-neutral-900/95 backdrop-blur-sm shadow-lg border-b border-white/5"
        x-data="{ menuOpen: false }">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        {{-- Logo --}}
        <a href="{{ route('home') }}"
           class="text-2xl sm:text-3xl font-extrabold tracking-widest
                  bg-gradient-to-r from-amber-400 to-yellow-300
                  bg-clip-text text-transparent flex-shrink-0">
            MISALO
        </a>

        {{-- Navigation desktop --}}
        <nav class="hidden md:flex gap-8 text-sm uppercase tracking-wider">
            <a href="{{ route('home') }}"
               class="{{ request()->routeIs('home') ? 'text-amber-400' : 'text-white hover:text-amber-400' }} transition duration-300">
               Accueil
            </a>
            <a href="{{ route('hotel') }}"
               class="{{ request()->routeIs('hotel') ? 'text-amber-400' : 'text-white hover:text-amber-400' }} transition duration-300">
               Hôtel
            </a>
            <a href="{{ route('restaurant') }}"
               class="{{ request()->routeIs('restaurant') ? 'text-amber-400' : 'text-white hover:text-amber-400' }} transition duration-300">
               Restaurant
            </a>
            <a href="{{ route('contact') }}"
               class="{{ request()->routeIs('contact') ? 'text-amber-400' : 'text-white hover:text-amber-400' }} transition duration-300">
               Contact
            </a>
        </nav>

        {{-- Boutons desktop --}}
        <div class="hidden md:flex gap-3">
            <a href="{{ route('login') }}"
               class="px-5 py-2 text-sm border border-amber-400 text-amber-400
                      rounded-full hover:bg-amber-400 hover:text-black transition duration-300">
                Connexion
            </a>
            <a href="{{ route('register') }}"
               class="px-5 py-2 text-sm bg-amber-400 text-black
                      rounded-full hover:bg-amber-300 transition duration-300 shadow-md">
                Inscription
            </a>
        </div>

        {{-- Bouton hamburger mobile --}}
        <button @click="menuOpen = !menuOpen"
                class="md:hidden w-10 h-10 flex items-center justify-center
                       rounded-xl border border-white/20 text-white/80
                       hover:border-amber-400/60 hover:text-amber-400 transition">
            <svg x-show="!menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <svg x-show="menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Menu mobile déroulant --}}
    <div x-show="menuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden bg-neutral-900 border-t border-white/10 px-6 pb-5"
         style="display:none">

        <nav class="flex flex-col gap-1 pt-3">
            <a href="{{ route('home') }}"
               class="px-4 py-3 rounded-xl text-sm uppercase tracking-wider
                      {{ request()->routeIs('home') ? 'text-amber-400 bg-amber-400/5' : 'text-white/70 hover:text-white hover:bg-white/5' }} transition">
                Accueil
            </a>
            <a href="{{ route('hotel') }}"
               class="px-4 py-3 rounded-xl text-sm uppercase tracking-wider
                      {{ request()->routeIs('hotel') ? 'text-amber-400 bg-amber-400/5' : 'text-white/70 hover:text-white hover:bg-white/5' }} transition">
                Hôtel
            </a>
            <a href="{{ route('restaurant') }}"
               class="px-4 py-3 rounded-xl text-sm uppercase tracking-wider
                      {{ request()->routeIs('restaurant') ? 'text-amber-400 bg-amber-400/5' : 'text-white/70 hover:text-white hover:bg-white/5' }} transition">
                Restaurant
            </a>
            <a href="{{ route('contact') }}"
               class="px-4 py-3 rounded-xl text-sm uppercase tracking-wider
                      {{ request()->routeIs('contact') ? 'text-amber-400 bg-amber-400/5' : 'text-white/70 hover:text-white hover:bg-white/5' }} transition">
                Contact
            </a>
        </nav>

        <div class="flex flex-col gap-3 mt-4 pt-4 border-t border-white/10">
            <a href="{{ route('login') }}"
               class="w-full py-3 text-center text-sm border border-amber-400 text-amber-400
                      rounded-full hover:bg-amber-400 hover:text-black transition">
                Connexion
            </a>
            <a href="{{ route('register') }}"
               class="w-full py-3 text-center text-sm bg-amber-400 text-black
                      rounded-full hover:bg-amber-300 transition shadow-md">
                Inscription
            </a>
        </div>
    </div>
</header>