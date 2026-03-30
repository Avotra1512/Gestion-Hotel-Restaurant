@extends('layouts.dashboard')
@section('title', 'Restaurant MISALO')
@include('components.nav-client')
@section('content')

@if(session('success'))
<div class="mb-4 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Restaurant MISALO</h2>
        <p class="text-white/50 text-sm mt-1">Parcourez notre carte et composez votre repas</p>
    </div>

    {{-- Bouton panier flottant --}}
    @if($nbArticles > 0)
    <a href="{{ route('client.restaurant.recapitulatif') }}"
       class="flex items-center gap-3 px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-300
              text-black font-semibold rounded-full shadow-lg shadow-amber-400/30
              hover:scale-[1.03] transition-all duration-300 text-sm">
        🛒 Mon panier
        <span class="px-2 py-0.5 bg-black/20 rounded-full text-xs font-bold">{{ $nbArticles }}</span>
        <span class="font-bold">{{ number_format($totalPanier, 0, ',', ' ') }} Ar</span>
    </a>
    @endif
</div>

{{-- MENU PAR CATÉGORIE --}}
@php
    $labelsCategories = [
        'entree'         => ['label' => 'Entrées',         'emoji' => '🥗'],
        'plat_principal' => ['label' => 'Plats principaux','emoji' => '🍖'],
        'dessert'        => ['label' => 'Desserts',        'emoji' => '🍰'],
        'fast_food'      => ['label' => 'Fast Food',       'emoji' => '🍔'],
        'boisson'        => ['label' => 'Boissons',        'emoji' => '🥤'],
    ];
@endphp

@foreach($ordreCategories as $cat)
    @if($menus->has($cat))
    <div class="mb-12">
        {{-- Titre de catégorie --}}
        <div class="flex items-center gap-3 mb-6">
            <span class="text-3xl">{{ $labelsCategories[$cat]['emoji'] ?? '🍽️' }}</span>
            <div>
                <h3 class="text-white font-bold text-xl">{{ $labelsCategories[$cat]['label'] ?? ucfirst($cat) }}</h3>
                <p class="text-white/40 text-xs">{{ $menus[$cat]->count() }} plat(s) disponible(s)</p>
            </div>
            <div class="flex-1 h-px bg-white/5 ml-4"></div>
        </div>

        {{-- Grille des plats --}}
        <div class="grid md:grid-cols-3 xl:grid-cols-3 gap-5">
            @foreach($menus[$cat] as $menu)
            <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden
                        hover:border-amber-400/20 hover:-translate-y-1 transition-all duration-300 flex flex-col">

                {{-- Image --}}
                <div class="h-44 bg-neutral-900 overflow-hidden relative">
                    @if($menu->image)
                        <img src="{{ Storage::url($menu->image) }}" alt="{{ $menu->nom }}"
                             class="w-full h-full object-cover hover:scale-105 transition duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-5xl text-white/10">
                            {{ $labelsCategories[$cat]['emoji'] ?? '🍽️' }}
                        </div>
                    @endif
                    {{-- Badge catégorie --}}
                    <div class="absolute top-2 left-2">
                        <span class="px-2.5 py-1 text-xs rounded-full border backdrop-blur-sm {{ $menu->couleurCategorie() }}">
                            {{ $menu->libelleCategorie() }}
                        </span>
                    </div>
                </div>

                {{-- Contenu --}}
                <div class="p-5 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="text-white font-semibold">{{ $menu->nom }}</h4>
                        <span class="text-amber-400 font-bold text-sm ml-3 whitespace-nowrap">
                            {{ number_format($menu->prix, 0, ',', ' ') }} Ar
                        </span>
                    </div>
                    @if($menu->description)
                        <p class="text-white/40 text-xs leading-relaxed mb-4 flex-grow">{{ $menu->description }}</p>
                    @endif

                    {{-- Formulaire ajout panier --}}
                    <form method="POST" action="{{ route('client.restaurant.panier.ajouter') }}" class="mt-auto">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <div class="flex items-center gap-2">
                            <input type="number" name="quantite" value="1" min="1" max="20"
                                   class="w-16 bg-black/40 border border-white/10 text-white text-center
                                          rounded-xl px-2 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                            <button type="submit"
                                    class="flex-1 py-2 bg-gradient-to-r from-amber-400 to-amber-300
                                           text-black font-semibold rounded-full text-xs
                                           hover:scale-[1.02] transition-all shadow-md shadow-amber-400/20">
                                + Ajouter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
@endforeach

{{-- PANIER FLOTTANT BAS si non vide --}}
@if($nbArticles > 0)
<div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
    <a href="{{ route('client.restaurant.recapitulatif') }}"
       class="flex items-center gap-4 px-8 py-4 bg-amber-400 text-black font-bold rounded-full
              shadow-2xl shadow-amber-400/40 hover:bg-amber-300 hover:scale-[1.03] transition-all duration-300">
        <span class="text-lg">🛒</span>
        <span>{{ $nbArticles }} article(s)</span>
        <span class="w-px h-4 bg-black/20"></span>
        <span>{{ number_format($totalPanier, 0, ',', ' ') }} Ar</span>
        <span>→ Voir le panier</span>
    </a>
</div>
@endif

@endsection