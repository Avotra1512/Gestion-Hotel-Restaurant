{{-- resources/views/admin/chambres/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Gestion des Chambres')

@include('components.nav-admin')

@section('content')

{{-- FLASH MESSAGES --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3 animate-fadeIn">
        <span class="text-lg">✅</span>
        {{ session('success') }}
    </div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Gestion des Chambres</h2>
        <p class="text-white/50 text-sm mt-1">Administrez le catalogue des chambres de l'hôtel MISALO</p>
    </div>
    <a href="{{ route('admin.chambres.create') }}"
       class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-300
              text-black font-semibold rounded-full shadow-lg shadow-amber-400/20
              hover:scale-[1.03] transition-all duration-300 text-sm">
        <span class="text-lg">+</span>
        Ajouter une chambre
    </a>
</div>

{{-- CARTES STATISTIQUES --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total Chambres</p>
        <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-green-400/30 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Disponibles</p>
        <p class="text-3xl font-bold text-green-400">{{ $stats['disponibles'] }}</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-red-400/30 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Occupées</p>
        <p class="text-3xl font-bold text-red-400">{{ $stats['occupees'] }}</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-neutral-400/30 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Hors Service</p>
        <p class="text-3xl font-bold text-neutral-400">{{ $stats['hors_service'] }}</p>
    </div>
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.chambres.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Numéro de chambre..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Type</label>
            <select name="type_chambre"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous les types</option>
                <option value="simple"  {{ request('type_chambre') === 'simple'  ? 'selected' : '' }}>Simple</option>
                <option value="double"  {{ request('type_chambre') === 'double'  ? 'selected' : '' }}>Double</option>
                <option value="triple"  {{ request('type_chambre') === 'triple'  ? 'selected' : '' }}>Triple</option>
            </select>
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Statut</label>
            <select name="statut"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous les statuts</option>
                <option value="disponible"  {{ request('statut') === 'disponible'  ? 'selected' : '' }}>Disponible</option>
                <option value="occupee"     {{ request('statut') === 'occupee'     ? 'selected' : '' }}>Occupée</option>
                <option value="hors_service"{{ request('statut') === 'hors_service'? 'selected' : '' }}>Hors service</option>
            </select>
        </div>
        <button type="submit"
                class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">
            Filtrer
        </button>
        <a href="{{ route('admin.chambres.index') }}"
           class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            Réinitialiser
        </a>
    </form>
</div>

{{-- TABLEAU DES CHAMBRES --}}
@if ($chambres->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
        <p class="text-5xl mb-4">🛏️</p>
        <p class="text-white/60 text-lg">Aucune chambre trouvée.</p>
        <a href="{{ route('admin.chambres.create') }}" class="mt-4 inline-block text-amber-400 hover:text-amber-300 transition text-sm">
            Ajouter la première chambre →
        </a>
    </div>
@else
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-white/50 text-xs uppercase tracking-widest">
                    <th class="text-left px-6 py-4">Chambre</th>
                    <th class="text-left px-6 py-4">Type</th>
                    <th class="text-left px-6 py-4">Prix / nuit</th>
                    <th class="text-left px-6 py-4">Équipements</th>
                    <th class="text-left px-6 py-4">Statut</th>
                    <th class="text-right px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach ($chambres as $chambre)
                <tr class="hover:bg-white/5 transition group">

                    {{-- Image + Numéro --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($chambre->image)
                                <img src="{{ Storage::url($chambre->image) }}"
                                     alt="{{ $chambre->numero_chambre }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-white/10">
                            @else
                                <div class="w-12 h-12 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-xl">
                                    🛏️
                                </div>
                            @endif
                            <div>
                                <p class="text-white font-semibold">{{ $chambre->numero_chambre }}</p>
                                <p class="text-white/40 text-xs">ID #{{ $chambre->id }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Type --}}
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $chambre->type_chambre === 'simple' ? 'bg-blue-400/10 text-blue-400 border border-blue-400/20' : '' }}
                            {{ $chambre->type_chambre === 'double' ? 'bg-purple-400/10 text-purple-400 border border-purple-400/20' : '' }}
                            {{ $chambre->type_chambre === 'triple' ? 'bg-amber-400/10 text-amber-400 border border-amber-400/20' : '' }}
                        ">
                            {{ ucfirst($chambre->type_chambre) }}
                        </span>
                    </td>

                    {{-- Prix --}}
                    <td class="px-6 py-4">
                        <span class="text-amber-400 font-semibold">
                            {{ number_format($chambre->prix_nuit, 0, ',', ' ') }} Ar
                        </span>
                    </td>

                    {{-- Équipements --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1 max-w-[200px]">
                            @if ($chambre->equipements && count($chambre->equipements) > 0)
                                @foreach (array_slice($chambre->equipements, 0, 3) as $eq)
                                    <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded-full text-xs text-white/60">
                                        {{ $eq }}
                                    </span>
                                @endforeach
                                @if (count($chambre->equipements) > 3)
                                    <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded-full text-xs text-white/40">
                                        +{{ count($chambre->equipements) - 3 }}
                                    </span>
                                @endif
                            @else
                                <span class="text-white/30 text-xs">—</span>
                            @endif
                        </div>
                    </td>

                    {{-- Statut --}}
                    <td class="px-6 py-4">
                        @if ($chambre->statut === 'disponible')
                            <span class="flex items-center gap-2 text-green-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                Disponible
                            </span>
                        @elseif ($chambre->statut === 'occupee')
                            <span class="flex items-center gap-2 text-red-400 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                Occupée
                            </span>
                        @else
                            <span class="flex items-center gap-2 text-neutral-500 text-xs font-medium">
                                <span class="w-2 h-2 rounded-full bg-neutral-500"></span>
                                Hors service
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Modifier --}}
                            <a href="{{ route('admin.chambres.edit', $chambre) }}"
                               class="px-4 py-1.5 border border-amber-400/30 text-amber-400 rounded-full text-xs
                                      hover:bg-amber-400 hover:text-black transition-all duration-200">
                                Modifier
                            </a>

                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('admin.chambres.destroy', $chambre) }}"
                                  onsubmit="return confirm('Supprimer la chambre {{ $chambre->numero_chambre }} ? Cette action est irréversible.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-1.5 border border-red-500/30 text-red-400 rounded-full text-xs
                                               hover:bg-red-500 hover:text-white transition-all duration-200">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
