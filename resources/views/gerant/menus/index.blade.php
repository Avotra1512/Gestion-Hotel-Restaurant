@extends('layouts.dashboard')
@section('title', 'Menu Restaurant')
@include('components.nav-gerant')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Menu Restaurant</h2>
        <p class="text-white/50 text-sm mt-1">Gérez la disponibilité des plats</p>
    </div>

    {{-- Bandeau info restriction --}}
    <div class="flex items-center gap-2 px-4 py-2 bg-white/5 border border-white/10 rounded-full">
        <span class="text-amber-400 text-sm">🔒</span>
        <p class="text-white/50 text-xs">Vue gérant — activation/désactivation uniquement</p>
    </div>
</div>

{{-- STATS --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total plats</p>
        <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Disponibles</p>
        <p class="text-3xl font-bold text-green-400">{{ $stats['disponibles'] }}</p>
    </div>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Indisponibles</p>
        <p class="text-3xl font-bold text-red-400">{{ $stats['indisponibles'] }}</p>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('gerant.menus.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom du plat..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Catégorie</label>
            <select name="categorie"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Toutes</option>
                <option value="entree"         {{ request('categorie')==='entree'         ? 'selected':'' }}>Entrée</option>
                <option value="plat_principal" {{ request('categorie')==='plat_principal' ? 'selected':'' }}>Plat principal</option>
                <option value="dessert"        {{ request('categorie')==='dessert'        ? 'selected':'' }}>Dessert</option>
                <option value="boisson"        {{ request('categorie')==='boisson'        ? 'selected':'' }}>Boisson</option>
                <option value="fast_food"      {{ request('categorie')==='fast_food'      ? 'selected':'' }}>Fast Food</option>
            </select>
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Disponibilité</label>
            <select name="disponible"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="1" {{ request('disponible')==='1' ? 'selected':'' }}>Disponible</option>
                <option value="0" {{ request('disponible')==='0' ? 'selected':'' }}>Indisponible</option>
            </select>
        </div>
        <button type="submit"
                class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">
            Filtrer
        </button>
        <a href="{{ route('gerant.menus.index') }}"
           class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            Réinitialiser
        </a>
    </form>
</div>

{{-- TABLEAU DES PLATS --}}
@if($menus->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
        <p class="text-5xl mb-4">🍽️</p>
        <p class="text-white/60">Aucun plat trouvé.</p>
    </div>
@else

{{-- Grouper par catégorie --}}
@php
    $groupes = $menus->groupBy('categorie');
    $ordre   = ['entree','plat_principal','dessert','fast_food','boisson'];
    $labels  = [
        'entree'         => ['label'=>'Entrées',          'emoji'=>'🥗'],
        'plat_principal' => ['label'=>'Plats principaux', 'emoji'=>'🍖'],
        'dessert'        => ['label'=>'Desserts',         'emoji'=>'🍰'],
        'fast_food'      => ['label'=>'Fast Food',        'emoji'=>'🍔'],
        'boisson'        => ['label'=>'Boissons',         'emoji'=>'🥤'],
    ];
@endphp

@foreach($ordre as $cat)
@if($groupes->has($cat))
<div class="mb-8">

    {{-- Titre catégorie --}}
    <div class="flex items-center gap-3 mb-4">
        <span class="text-2xl">{{ $labels[$cat]['emoji'] ?? '🍽️' }}</span>
        <h3 class="text-white font-bold">{{ $labels[$cat]['label'] ?? ucfirst($cat) }}</h3>
        <span class="text-white/30 text-xs">{{ $groupes[$cat]->count() }} plat(s)</span>
        <div class="flex-1 h-px bg-white/5 ml-2"></div>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-white/50 text-xs uppercase tracking-widest">
                    <th class="text-left px-5 py-3">Plat</th>
                    <th class="text-left px-5 py-3">Description</th>
                    <th class="text-left px-5 py-3">Prix</th>
                    <th class="text-left px-5 py-3">Statut</th>
                    <th class="text-center px-5 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($groupes[$cat] as $menu)
                <tr class="hover:bg-white/5 transition {{ !$menu->disponible ? 'opacity-60' : '' }}">

                    {{-- Nom + image --}}
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                                @if($menu->image)
                                    <img src="{{ Storage::url($menu->image) }}"
                                         alt="{{ $menu->nom }}"
                                         class="w-full h-full object-cover {{ !$menu->disponible ? 'grayscale' : '' }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-lg text-white/20">
                                        {{ $labels[$cat]['emoji'] ?? '🍽️' }}
                                    </div>
                                @endif
                            </div>
                            <span class="text-white font-medium">{{ $menu->nom }}</span>
                        </div>
                    </td>

                    {{-- Description --}}
                    <td class="px-5 py-4">
                        <p class="text-white/40 text-xs line-clamp-2 max-w-[220px]">
                            {{ $menu->description ?? '—' }}
                        </p>
                    </td>

                    {{-- Prix --}}
                    <td class="px-5 py-4">
                        <span class="text-amber-400 font-semibold">
                            {{ number_format($menu->prix, 0, ',', ' ') }} Ar
                        </span>
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-4">
                        @if($menu->disponible)
                            <span class="flex items-center gap-1.5 text-green-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                Disponible
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-red-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                Indisponible
                            </span>
                        @endif
                    </td>

                    {{-- Toggle disponibilité — SEULE action autorisée --}}
                    <td class="px-5 py-4 text-center">
                        <form method="POST" action="{{ route('gerant.menus.toggle-disponible', $menu) }}">
                            @csrf @method('PATCH')

                            {{-- Toggle switch visuel --}}
                            <button type="submit"
                                    title="{{ $menu->disponible ? 'Désactiver ce plat' : 'Activer ce plat' }}"
                                    class="relative inline-flex items-center w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none
                                           {{ $menu->disponible ? 'bg-green-500' : 'bg-white/20' }}">
                                <span class="inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-300
                                             {{ $menu->disponible ? 'translate-x-7' : 'translate-x-1' }}">
                                </span>
                            </button>

                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endforeach

@endif

{{-- NOTE RESTRICTION --}}
<div class="mt-8 p-4 bg-white/5 border border-white/10 rounded-2xl flex items-start gap-3">
    <span class="text-amber-400 text-xl mt-0.5">🔒</span>
    <div>
        <p class="text-white/70 text-sm font-medium">Accès restreint</p>
        <p class="text-white/40 text-xs mt-1 leading-relaxed">
            En tant que gérant, vous pouvez uniquement activer ou désactiver la disponibilité des plats.
            Pour ajouter, modifier ou supprimer un plat, veuillez contacter l'administrateur.
        </p>
    </div>
</div>

@endsection