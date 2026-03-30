@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4 mt-6">

    {{-- INFO : "Résultats X à Y sur Z" --}}
    <div class="text-white/40 text-xs hidden sm:block">
        Affichage de
        <span class="text-white/70 font-medium">{{ $paginator->firstItem() }}</span>
        à
        <span class="text-white/70 font-medium">{{ $paginator->lastItem() }}</span>
        sur
        <span class="text-white/70 font-medium">{{ $paginator->total() }}</span>
        résultats
    </div>

    {{-- BOUTONS --}}
    <div class="flex items-center gap-1">

        {{-- Bouton Précédent --}}
        @if ($paginator->onFirstPage())
            <span class="px-4 py-2 rounded-xl text-xs text-white/20 border border-white/5 cursor-not-allowed select-none">
                ← Précédent
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-4 py-2 rounded-xl text-xs text-white/60 border border-white/10
                      hover:bg-white/5 hover:text-amber-400 hover:border-amber-400/30
                      transition-all duration-200">
                ← Précédent
            </a>
        @endif

        {{-- Numéros de pages --}}
        @foreach ($elements as $element)

            {{-- Séparateur "..." --}}
            @if (is_string($element))
                <span class="px-3 py-2 text-xs text-white/30 select-none">{{ $element }}</span>
            @endif

            {{-- Pages --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        {{-- Page active --}}
                        <span class="px-4 py-2 rounded-xl text-xs font-bold
                                     bg-amber-400 text-black shadow-lg shadow-amber-400/20
                                     select-none">
                            {{ $page }}
                        </span>
                    @else
                        {{-- Autres pages --}}
                        <a href="{{ $url }}"
                           class="px-4 py-2 rounded-xl text-xs text-white/60 border border-white/10
                                  hover:bg-white/5 hover:text-amber-400 hover:border-amber-400/30
                                  transition-all duration-200">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- Bouton Suivant --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-4 py-2 rounded-xl text-xs text-white/60 border border-white/10
                      hover:bg-white/5 hover:text-amber-400 hover:border-amber-400/30
                      transition-all duration-200">
                Suivant →
            </a>
        @else
            <span class="px-4 py-2 rounded-xl text-xs text-white/20 border border-white/5 cursor-not-allowed select-none">
                Suivant →
            </span>
        @endif

    </div>
</nav>
@endif