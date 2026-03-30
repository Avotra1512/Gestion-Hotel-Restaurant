@extends('layouts.dashboard')
@section('title', 'Dashboard Admin')
@include('components.nav-admin')
@section('content')

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Console Admin</h2>
        <p class="text-white/50 text-sm mt-1">
            Vue globale du système — {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </p>
    </div>
    <div class="flex items-center gap-2 px-4 py-2 bg-amber-400/10 border border-amber-400/20 rounded-full">
        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
        <span class="text-amber-400 text-xs font-medium">Système opérationnel</span>
    </div>
</div>

{{-- ALERTES --}}
@if($stats['reservations_attente'] > 0 || $stats['commandes_attente'] > 0)
<div class="grid md:grid-cols-2 gap-4 mb-6">
    @if($stats['reservations_attente'] > 0)
    <div class="p-4 bg-amber-400/10 border border-amber-400/30 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">⚠️</span>
            <span class="text-amber-400 text-sm">
                <strong>{{ $stats['reservations_attente'] }}</strong> réservation(s) en attente
            </span>
        </div>
        <a href="{{ route('admin.chambres.index') }}"
           class="px-3 py-1 bg-amber-400 text-black rounded-full text-xs font-bold hover:bg-amber-300 transition">
            Voir →
        </a>
    </div>
    @endif
    @if($stats['commandes_attente'] > 0)
    <div class="p-4 bg-blue-400/10 border border-blue-400/30 rounded-xl flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-xl">🍽️</span>
            <span class="text-blue-400 text-sm">
                <strong>{{ $stats['commandes_attente'] }}</strong> commande(s) en cours
            </span>
        </div>
    </div>
    @endif
</div>
@endif

{{-- REVENUS GLOBAUX --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    {{-- Revenu total --}}
    <div class="md:col-span-1 bg-gradient-to-br from-amber-400/15 to-amber-300/5
                border border-amber-400/30 rounded-2xl p-6">
        <p class="text-amber-400/70 text-xs uppercase tracking-widest mb-2">Revenus totaux</p>
        <p class="text-3xl font-bold text-white">{{ number_format($stats['revenus_total'], 0, ',', ' ') }}</p>
        <p class="text-amber-400/60 text-sm mt-1">Ariary</p>
        <div class="mt-4 pt-4 border-t border-amber-400/20 space-y-2">
            <div class="flex justify-between text-xs">
                <span class="text-white/50">🛏️ Chambres</span>
                <span class="text-white/80">{{ number_format($stats['revenus_chambres_total'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-white/50">🍽️ Restaurant</span>
                <span class="text-white/80">{{ number_format($stats['revenus_restaurant_total'], 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>

    {{-- Revenu ce mois --}}
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-6">
        <p class="text-green-400/70 text-xs uppercase tracking-widest mb-2">Revenus ce mois</p>
        <p class="text-3xl font-bold text-green-400">{{ number_format($stats['revenus_mois_total'], 0, ',', ' ') }}</p>
        <p class="text-white/30 text-sm mt-1">Ariary</p>
        <div class="mt-4 pt-4 border-t border-white/10 space-y-2">
            <div class="flex justify-between text-xs">
                <span class="text-white/50">🛏️ Chambres</span>
                <span class="text-white/80">{{ number_format($stats['revenus_chambres_mois'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-white/50">🍽️ Restaurant</span>
                <span class="text-white/80">{{ number_format($stats['revenus_restaurant_mois'], 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>

    {{-- Taux d'occupation --}}
    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-6">
        <p class="text-blue-400/70 text-xs uppercase tracking-widest mb-2">Taux d'occupation</p>
        <p class="text-3xl font-bold text-blue-400">{{ $taux_occupation }}%</p>
        <p class="text-white/30 text-sm mt-1">des chambres</p>
        <div class="mt-4">
            <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full bg-gradient-to-r from-blue-400 to-blue-300 transition-all"
                     style="width: {{ $taux_occupation }}%"></div>
            </div>
            <p class="text-white/30 text-xs mt-2">
                {{ $stats['chambres_occupees'] }} occupée(s) sur {{ $stats['chambres_total'] }}
            </p>
        </div>
    </div>
</div>

{{-- GRILLE DE STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <a href="{{ route('admin.chambres.index') }}"
       class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 hover:-translate-y-0.5 transition-all group">
        <div class="flex justify-between items-start mb-3">
            <p class="text-white/50 text-xs uppercase tracking-widest">Chambres</p>
            <span class="text-2xl">🛏️</span>
        </div>
        <p class="text-3xl font-bold text-white group-hover:text-amber-400 transition">{{ $stats['chambres_total'] }}</p>
        <div class="mt-3 flex gap-3 text-xs">
            <span class="text-green-400">{{ $stats['chambres_disponibles'] }} dispo</span>
            <span class="text-red-400">{{ $stats['chambres_occupees'] }} occ.</span>
        </div>
    </a>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 transition">
        <div class="flex justify-between items-start mb-3">
            <p class="text-white/50 text-xs uppercase tracking-widest">Réservations</p>
            <span class="text-2xl">📅</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['reservations_total'] }}</p>
        <div class="mt-3 flex gap-3 text-xs">
            <span class="text-amber-400">{{ $stats['reservations_attente'] }} en att.</span>
            <span class="text-white/30">+{{ $stats['reservations_mois'] }} ce mois</span>
        </div>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 transition">
        <div class="flex justify-between items-start mb-3">
            <p class="text-white/50 text-xs uppercase tracking-widest">Commandes</p>
            <span class="text-2xl">🍽️</span>
        </div>
        <p class="text-3xl font-bold text-white">{{ $stats['commandes_total'] }}</p>
        <div class="mt-3 flex gap-3 text-xs">
            <span class="text-blue-400">{{ $stats['commandes_attente'] }} actives</span>
            <span class="text-white/30">+{{ $stats['commandes_mois'] }} ce mois</span>
        </div>
    </div>

    <a href="#"
       class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 hover:-translate-y-0.5 transition-all group">
        <div class="flex justify-between items-start mb-3">
            <p class="text-white/50 text-xs uppercase tracking-widest">Clients</p>
            <span class="text-2xl">👥</span>
        </div>
        <p class="text-3xl font-bold text-white group-hover:text-amber-400 transition">{{ $stats['clients_total'] }}</p>
        <div class="mt-3 text-xs">
            <span class="text-green-400">+{{ $stats['clients_mois'] }} ce mois</span>
        </div>
    </a>
</div>

{{-- LIGNE : Menus --}}
<div class="grid grid-cols-2 gap-4 mb-8">
    <a href="{{ route('admin.menus.index') }}"
       class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 transition group flex items-center gap-4">
        <span class="text-3xl">🍴</span>
        <div>
            <p class="text-white font-semibold group-hover:text-amber-400 transition">
                {{ $stats['menus_total'] }} plats au menu
            </p>
            <p class="text-white/40 text-xs mt-1">
                {{ $stats['menus_disponibles'] }} disponibles · {{ $stats['menus_total'] - $stats['menus_disponibles'] }} indisponibles
            </p>
        </div>
        <span class="ml-auto text-white/30 group-hover:text-amber-400 transition">→</span>
    </a>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 flex items-center gap-4">
        <span class="text-3xl">📊</span>
        <div>
            <p class="text-white font-semibold">
                {{ $stats['reservations_mois'] + $stats['commandes_mois'] }} opérations ce mois
            </p>
            <p class="text-white/40 text-xs mt-1">
                {{ $stats['reservations_mois'] }} rés. · {{ $stats['commandes_mois'] }} cmd.
            </p>
        </div>
    </div>
</div>

{{-- ÉVOLUTION 7 JOURS --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-6">
    <h3 class="text-white font-semibold mb-6">Revenus — 7 derniers jours</h3>
    @php $maxVal = $evolution->max(fn($e) => $e['chambres'] + $e['restaurant']) ?: 1; @endphp
    <div class="space-y-3">
        @foreach($evolution as $jour)
        @php $total = $jour['chambres'] + $jour['restaurant']; @endphp
        <div class="flex items-center gap-4">
            <div class="w-16 text-right flex-shrink-0">
                <p class="text-white/60 text-xs">{{ $jour['date'] }}</p>
                <p class="text-white/30 text-[10px] capitalize">{{ mb_substr($jour['jour'], 0, 3) }}</p>
            </div>
            <div class="flex-1 space-y-1">
                @if($jour['chambres'] > 0)
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-amber-400/60 rounded-full"
                             style="width: {{ ($jour['chambres'] / $maxVal) * 100 }}%"></div>
                    </div>
                    <span class="text-[10px] text-amber-400/60 w-24 text-right">{{ number_format($jour['chambres'], 0, ',', ' ') }} Ar</span>
                </div>
                @endif
                @if($jour['restaurant'] > 0)
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-blue-400/60 rounded-full"
                             style="width: {{ ($jour['restaurant'] / $maxVal) * 100 }}%"></div>
                    </div>
                    <span class="text-[10px] text-blue-400/60 w-24 text-right">{{ number_format($jour['restaurant'], 0, ',', ' ') }} Ar</span>
                </div>
                @endif
                @if($jour['chambres'] === 0 && $jour['restaurant'] === 0)
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-white/5 rounded-full h-2"></div>
                    <span class="text-[10px] text-white/20 w-24 text-right">Aucun revenu</span>
                </div>
                @endif
            </div>
            <div class="w-28 text-right flex-shrink-0">
                <p class="text-white font-semibold text-sm">{{ number_format($total, 0, ',', ' ') }} Ar</p>
            </div>
        </div>
        @endforeach
    </div>
    <div class="flex items-center gap-6 mt-5 pt-4 border-t border-white/10">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-amber-400/60"></div>
            <span class="text-white/40 text-xs">Chambres</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-blue-400/60"></div>
            <span class="text-white/40 text-xs">Restaurant</span>
        </div>
    </div>
</div>

{{-- TABLEAUX : Dernières activités --}}
<div class="grid lg:grid-cols-3 gap-6">

    {{-- Dernières réservations --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white font-semibold text-sm">🛏️ Dernières réservations</h3>
            <a href="{{ route('admin.chambres.index') }}"
               class="text-amber-400/70 text-xs hover:text-amber-400 transition">Voir →</a>
        </div>
        @forelse($dernieresReservations as $res)
        <div class="px-5 py-3 border-b border-white/5 hover:bg-white/5 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white text-xs font-medium">{{ $res->nom }}</p>
                    <p class="text-white/40 text-[10px] mt-0.5">
                        {{ $res->chambre?->numero_chambre ?? '—' }}
                        · #{{ str_pad($res->id, 5, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $res->couleurStatut() }}">
                    {{ $res->libelleStatut() }}
                </span>
            </div>
            <p class="text-amber-400/70 text-[10px] mt-1 font-medium">
                {{ number_format($res->prix_total, 0, ',', ' ') }} Ar
            </p>
        </div>
        @empty
        <div class="p-8 text-center text-white/30 text-xs">Aucune réservation.</div>
        @endforelse
    </div>

    {{-- Dernières commandes --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white font-semibold text-sm">🍽️ Dernières commandes</h3>
        </div>
        @forelse($dernieresCommandes as $cmd)
        <div class="px-5 py-3 border-b border-white/5 hover:bg-white/5 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-white text-xs font-medium">{{ $cmd->nom }}</p>
                    <p class="text-white/40 text-[10px] mt-0.5">
                        {{ $cmd->items->count() }} plat(s)
                        · {{ $cmd->created_at->format('H:i') }}
                    </p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $cmd->couleurStatut() }}">
                    {{ $cmd->libelleStatut() }}
                </span>
            </div>
            <p class="text-amber-400/70 text-[10px] mt-1 font-medium">
                {{ number_format($cmd->total, 0, ',', ' ') }} Ar
            </p>
        </div>
        @empty
        <div class="p-8 text-center text-white/30 text-xs">Aucune commande.</div>
        @endforelse
    </div>

    {{-- Nouveaux clients + Top menus --}}
    <div class="space-y-4">

        {{-- Nouveaux clients --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10">
                <h3 class="text-white font-semibold text-sm">👥 Nouveaux clients</h3>
            </div>
            @forelse($nouveauxClients as $client)
            <div class="px-5 py-3 border-b border-white/5 flex items-center gap-3 hover:bg-white/5 transition">
                <div class="w-7 h-7 rounded-full bg-amber-400/10 border border-amber-400/20
                            flex items-center justify-center text-xs font-bold text-amber-400 flex-shrink-0">
                    {{ strtoupper(substr($client->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-white text-xs font-medium">{{ $client->name }}</p>
                    <p class="text-white/30 text-[10px]">{{ $client->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-white/30 text-xs">Aucun client récent.</div>
            @endforelse
        </div>

        {{-- Top menus --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-semibold text-sm">🏆 Top plats ce mois</h3>
            </div>
            @forelse($topMenus as $index => $item)
            <div class="px-5 py-3 border-b border-white/5 flex items-center gap-3 hover:bg-white/5 transition">
                <span class="text-xs font-bold w-5 text-center
                             {{ $index === 0 ? 'text-amber-400' : 'text-white/30' }}">
                    {{ $index + 1 }}
                </span>
                <p class="text-white text-xs flex-grow">{{ $item->menu?->nom ?? '—' }}</p>
                <span class="text-amber-400/70 text-xs font-bold">×{{ $item->total_commande }}</span>
            </div>
            @empty
            <div class="p-6 text-center text-white/30 text-xs">Aucune donnée.</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ACTIONS RAPIDES ADMIN --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
    <a href="{{ route('admin.chambres.create') }}"
       class="flex flex-col items-center gap-2 p-5 bg-white/5 border border-white/10 rounded-2xl
              hover:border-amber-400/30 hover:-translate-y-1 transition-all group text-center">
        <span class="text-2xl">➕</span>
        <p class="text-white/70 text-xs group-hover:text-amber-400 transition">Ajouter une chambre</p>
    </a>
    <a href="{{ route('admin.menus.create') }}"
       class="flex flex-col items-center gap-2 p-5 bg-white/5 border border-white/10 rounded-2xl
              hover:border-amber-400/30 hover:-translate-y-1 transition-all group text-center">
        <span class="text-2xl">🍴</span>
        <p class="text-white/70 text-xs group-hover:text-amber-400 transition">Ajouter un plat</p>
    </a>
    <a href="{{ route('admin.chambres.index') }}"
       class="flex flex-col items-center gap-2 p-5 bg-white/5 border border-white/10 rounded-2xl
              hover:border-amber-400/30 hover:-translate-y-1 transition-all group text-center">
        <span class="text-2xl">🛏️</span>
        <p class="text-white/70 text-xs group-hover:text-amber-400 transition">Gérer les chambres</p>
    </a>
    <a href="{{ route('admin.menus.index') }}"
       class="flex flex-col items-center gap-2 p-5 bg-white/5 border border-white/10 rounded-2xl
              hover:border-amber-400/30 hover:-translate-y-1 transition-all group text-center">
        <span class="text-2xl">📋</span>
        <p class="text-white/70 text-xs group-hover:text-amber-400 transition">Gérer les menus</p>
    </a>
</div>

@endsection