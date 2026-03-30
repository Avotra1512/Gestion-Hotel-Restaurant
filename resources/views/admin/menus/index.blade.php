@extends('layouts.dashboard')
@section('title', 'Menus Restaurant')
@include('components.nav-admin')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Menus Restaurant</h2>
        <p class="text-white/50 text-sm mt-1">Gérez la carte du restaurant MISALO</p>
    </div>
    <a href="{{ route('admin.menus.create') }}"
       class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-300
              text-black font-semibold rounded-full shadow-lg shadow-amber-400/20
              hover:scale-[1.03] transition-all duration-300 text-sm">
        + Ajouter un plat
    </a>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xl">🍽️</div>
        <div>
            <p class="text-white/50 text-xs uppercase tracking-widest">Total</p>
            <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
        </div>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-green-400/10 flex items-center justify-center text-xl">✅</div>
        <div>
            <p class="text-white/50 text-xs uppercase tracking-widest">Disponibles</p>
            <p class="text-2xl font-bold text-green-400">{{ $stats['disponibles'] }}</p>
        </div>
    </div>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-red-400/10 flex items-center justify-center text-xl">⏸</div>
        <div>
            <p class="text-white/50 text-xs uppercase tracking-widest">Indisponibles</p>
            <p class="text-2xl font-bold text-red-400">{{ $stats['indisponibles'] }}</p>
        </div>
    </div>
    <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-400/10 flex items-center justify-center text-xl">📂</div>
        <div>
            <p class="text-white/50 text-xs uppercase tracking-widest">Catégories</p>
            <p class="text-2xl font-bold text-amber-400">{{ $stats['categories'] }}</p>
        </div>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-4 mb-8">
    <form method="GET" action="{{ route('admin.menus.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[160px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-1.5">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom du plat..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-3 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-1.5">Catégorie</label>
            <select name="categorie"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Toutes</option>
                <option value="entree"         {{ request('categorie')==='entree'         ? 'selected':'' }}>Entrée</option>
                <option value="plat_principal" {{ request('categorie')==='plat_principal' ? 'selected':'' }}>Plat principal</option>
                <option value="dessert"        {{ request('categorie')==='dessert'        ? 'selected':'' }}>Dessert</option>
                <option value="boisson"        {{ request('categorie')==='boisson'        ? 'selected':'' }}>Boisson</option>
                <option value="fast_food"      {{ request('categorie')==='fast_food'      ? 'selected':'' }}>Fast Food</option>
            </select>
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-1.5">Disponibilité</label>
            <select name="disponible"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="1" {{ request('disponible')==='1' ? 'selected':'' }}>Disponible</option>
                <option value="0" {{ request('disponible')==='0' ? 'selected':'' }}>Indisponible</option>
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">
            Filtrer
        </button>
        <a href="{{ route('admin.menus.index') }}"
           class="px-5 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            Réinitialiser
        </a>
    </form>
</div>

{{-- GRILLE DES MENUS --}}
@if($menus->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
        <p class="text-5xl mb-4">🍽️</p>
        <p class="text-white/60">Aucun plat trouvé.</p>
        <a href="{{ route('admin.menus.create') }}"
           class="mt-4 inline-block text-amber-400 hover:text-amber-300 transition text-sm">
            Ajouter le premier plat →
        </a>
    </div>
@else
    @php
        $categories = $menus->groupBy('categorie');
        $ordre = ['entree', 'plat_principal', 'dessert', 'boisson', 'fast_food'];

        $emojis = [
            'entree'         => '🥗',
            'plat_principal' => '🍖',
            'dessert'        => '🍰',
            'boisson'        => '🥤',
            'fast_food'      => '🍔',
        ];
    @endphp

    @foreach($ordre as $cat)
        @if($categories->has($cat))
        @php $premierMenu = $categories[$cat]->first(); @endphp

        <div class="mb-10">

            {{-- TITRE SECTION --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg
                            {{ str_replace(['border-','20'], ['bg-','10'], explode(' ', $premierMenu->couleurCategorie())[0]) }}">
                    {{ $emojis[$cat] ?? '🍽️' }}
                </div>
                <div>
                    <h3 class="text-white font-bold text-base">{{ $premierMenu->libelleCategorie() }}</h3>
                    <p class="text-white/30 text-xs">{{ $categories[$cat]->count() }} plat(s)</p>
                </div>
                <div class="flex-1 h-px bg-gradient-to-r from-white/10 to-transparent ml-2"></div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $premierMenu->couleurCategorie() }}">
                    {{ $categories[$cat]->count() }}
                </span>
            </div>

            {{-- GRILLE 4 COLONNES --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($categories[$cat] as $menu)
                <div class="bg-white/5 border rounded-2xl overflow-hidden transition-all duration-300
                            hover:-translate-y-1 hover:shadow-lg group
                            {{ $menu->disponible
                                ? 'border-white/10 hover:border-amber-400/30 hover:shadow-amber-400/5'
                                : 'border-white/5 opacity-60 hover:opacity-80' }}">

                    {{-- IMAGE COMPACTE --}}
                    <div class="relative h-28 bg-neutral-900 overflow-hidden">
                        @if($menu->image)
                            <img src="{{ Storage::url($menu->image) }}"
                                 alt="{{ $menu->nom }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        @else
                            {{-- Placeholder stylisé selon catégorie --}}
                            <div class="w-full h-full flex items-center justify-center
                                        bg-gradient-to-br from-white/5 to-transparent">
                                <span class="text-4xl opacity-20">{{ $emojis[$cat] ?? '🍽️' }}</span>
                            </div>
                        @endif

                        {{-- Overlay gradient pour lisibilité --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                        {{-- Prix en bas de l'image --}}
                        <div class="absolute bottom-2 left-2">
                            <span class="px-2 py-0.5 bg-black/60 backdrop-blur-sm border border-amber-400/30
                                         text-amber-400 text-xs font-bold rounded-lg">
                                {{ number_format($menu->prix, 0, ',', ' ') }} Ar
                            </span>
                        </div>

                        {{-- Badge disponibilité --}}
                        <div class="absolute top-2 right-2">
                            @if($menu->disponible)
                                <span class="w-2 h-2 rounded-full bg-green-400 block animate-pulse shadow-lg shadow-green-400/50"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-red-400 block shadow-lg shadow-red-400/50"></span>
                            @endif
                        </div>
                    </div>

                    {{-- CONTENU COMPACT --}}
                    <div class="p-3">

                        {{-- Nom --}}
                        <h3 class="text-white text-sm font-semibold leading-tight line-clamp-1 mb-1">
                            {{ $menu->nom }}
                        </h3>

                        {{-- Description courte --}}
                        @if($menu->description)
                            <p class="text-white/40 text-xs leading-relaxed line-clamp-2 mb-3 min-h-[2rem]">
                                {{ $menu->description }}
                            </p>
                        @else
                            <div class="mb-3 min-h-[2rem]"></div>
                        @endif

                        {{-- ACTIONS --}}
                        <div class="flex items-center gap-1.5 pt-2 border-t border-white/5">

                            {{-- Toggle disponible --}}
                            <form method="POST"
                                  action="{{ route('admin.menus.toggle-disponible', $menu) }}"
                                  class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        title="{{ $menu->disponible ? 'Désactiver' : 'Activer' }}"
                                        class="w-full py-1.5 rounded-lg text-xs border transition
                                               {{ $menu->disponible
                                                   ? 'border-red-500/20 text-red-400 hover:bg-red-500/10'
                                                   : 'border-green-500/20 text-green-400 hover:bg-green-500/10' }}">
                                    {{ $menu->disponible ? '⏸' : '▶' }}
                                </button>
                            </form>

                            {{-- Modifier --}}
                            <a href="{{ route('admin.menus.edit', $menu) }}"
                               title="Modifier"
                               class="flex-1 py-1.5 text-center border border-amber-400/30 text-amber-400
                                      rounded-lg text-xs hover:bg-amber-400/10 transition">
                                ✏️
                            </a>

                            {{-- Supprimer --}}
                            <form method="POST"
                                  action="{{ route('admin.menus.destroy', $menu) }}"
                                  onsubmit="return confirm('Supprimer « {{ addslashes($menu->nom) }} » ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        title="Supprimer"
                                        class="py-1.5 px-2.5 border border-red-500/20 text-red-400
                                               rounded-lg text-xs hover:bg-red-500/10 transition">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
@endif

@endsection