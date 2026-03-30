{{-- resources/views/client/chambres/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Nos Chambres')

@include('components.nav-client')

@section('content')

{{-- EN-TÊTE --}}
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-wide">Nos Chambres</h2>
    <p class="text-white/50 text-sm mt-1">Choisissez la chambre qui vous convient et réservez en quelques secondes</p>
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-8">
    <form method="GET" action="{{ route('client.chambres.index') }}" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Type de chambre</label>
            <select name="type_chambre"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm
                           focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous les types</option>
                <option value="simple" {{ request('type_chambre') === 'simple' ? 'selected' : '' }}>Simple</option>
                <option value="double" {{ request('type_chambre') === 'double' ? 'selected' : '' }}>Double</option>
                <option value="triple" {{ request('type_chambre') === 'triple' ? 'selected' : '' }}>Triple</option>
            </select>
        </div>
        <button type="submit"
                class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">
            Filtrer
        </button>
        <a href="{{ route('client.chambres.index') }}"
           class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            Réinitialiser
        </a>
    </form>
</div>

{{-- GRILLE DES CHAMBRES --}}
@if ($chambres->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
        <p class="text-5xl mb-4">🛏️</p>
        <p class="text-white/60 text-lg">Aucune chambre disponible pour le moment.</p>
    </div>
@else
    <div class="grid md:grid-cols-3 xl:grid-cols-3 gap-6">
        @foreach ($chambres as $chambre)
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden
                    hover:border-amber-400/30 hover:-translate-y-1 transition-all duration-300 flex flex-col">

            {{-- IMAGE --}}
            <div class="relative h-52 bg-neutral-900 overflow-hidden">
                @if ($chambre->image)
                    <img src="{{ Storage::url($chambre->image) }}"
                         alt="{{ $chambre->numero_chambre }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                @else
                    <div class="w-full h-full flex items-center justify-center text-5xl text-white/20">🛏️</div>
                @endif

                {{-- Badge statut — toujours disponible (on ne montre plus "occupée") --}}
                
                    <div class="absolute top-3 right-3">
                        @if ($chambre->statut === 'hors_service')
                            <span class="flex items-center gap-1.5 px-3 py-1 bg-neutral-500/20 border border-neutral-500/30
                                        text-neutral-400 text-xs font-medium rounded-full backdrop-blur-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-neutral-400"></span>
                                Hors service
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 px-3 py-1 bg-green-500/20 border border-green-500/30
                                        text-green-400 text-xs font-medium rounded-full backdrop-blur-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                                Disponible
                            </span>
                        @endif
                    </div>

                {{-- Badge type --}}
                <div class="absolute top-3 left-3">
                    <span class="px-3 py-1 bg-black/50 border border-white/10 text-white/80 text-xs
                                 rounded-full backdrop-blur-sm capitalize">
                        {{ $chambre->type_chambre }}
                    </span>
                </div>
            </div>

            {{-- CONTENU --}}
            <div class="p-5 flex flex-col flex-grow">

                {{-- Titre + Prix --}}
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-white font-bold text-lg">{{ $chambre->numero_chambre }}</h3>
                    <div class="text-right">
                        <span class="text-amber-400 font-bold text-xl">
                            {{ number_format($chambre->prix_nuit, 0, ',', ' ') }}
                        </span>
                        <span class="text-white/40 text-xs"> Ar/nuit</span>
                    </div>
                </div>

                {{-- Description --}}
                @if ($chambre->description)
                    <p class="text-white/50 text-sm leading-relaxed mb-4 line-clamp-2">
                        {{ $chambre->description }}
                    </p>
                @endif

                {{-- Équipements --}}
                @if ($chambre->equipements && count($chambre->equipements) > 0)
                    <div class="flex flex-wrap gap-1.5 mb-5">
                        @foreach (array_slice($chambre->equipements, 0, 4) as $eq)
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-white/60 text-xs rounded-full">
                                {{ $eq }}
                            </span>
                        @endforeach
                        @if (count($chambre->equipements) > 4)
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-white/40 text-xs rounded-full">
                                +{{ count($chambre->equipements) - 4 }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Bouton réserver --}}
                    <div class="mt-auto">
                        @if ($chambre->statut === 'hors_service')
                            <button disabled
                                    class="w-full py-3 bg-white/5 border border-white/10 text-white/30
                                        rounded-full text-sm cursor-not-allowed">
                                🚫 Chambre hors service
                            </button>
                        @else
                            <a href="{{ route('client.chambres.reserver', $chambre) }}"
                            class="w-full flex items-center justify-center gap-2 py-3
                                    bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                                    rounded-full shadow-lg shadow-amber-400/20
                                    hover:scale-[1.02] hover:shadow-amber-400/40 transition-all duration-300 text-sm">
                                Réserver cette chambre →
                            </a>
                        @endif
                    </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
