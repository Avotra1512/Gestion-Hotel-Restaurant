@extends('layouts.dashboard')
@section('title', 'Statistiques Ventes')
@include('components.nav-gerant')
@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white">Statistiques des Ventes</h2>
    <p class="text-white/50 text-sm mt-1">Analyse des revenus — chambres & restaurant</p>
</div>

{{-- ONGLETS PÉRIODE --}}
<div class="grid grid-cols-3 gap-4 mb-8">
    {{-- Aujourd'hui --}}
    <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-5">
        <p class="text-amber-400 text-xs uppercase tracking-widest font-bold mb-4">Aujourd'hui</p>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-white/50">🛏️ Chambres</span>
                <span class="text-white font-medium">{{ number_format($stats['revenu_jour_chambre'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">🍽️ Restaurant</span>
                <span class="text-white font-medium">{{ number_format($stats['revenu_jour_restaurant'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm border-t border-white/10 pt-3">
                <span class="text-white font-semibold">Total</span>
                <span class="text-amber-400 font-bold text-lg">{{ number_format($stats['revenu_jour_total'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-xs text-white/30 pt-1 border-t border-white/5">
                <span>{{ $stats['reservations_jour'] }} réservation(s)</span>
                <span>{{ $stats['commandes_jour'] }} commande(s)</span>
            </div>
        </div>
    </div>

    {{-- Ce mois --}}
    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-5">
        <p class="text-blue-400 text-xs uppercase tracking-widest font-bold mb-4">Ce mois</p>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-white/50">🛏️ Chambres</span>
                <span class="text-white font-medium">{{ number_format($stats['revenu_mois_chambre'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">🍽️ Restaurant</span>
                <span class="text-white font-medium">{{ number_format($stats['revenu_mois_restaurant'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm border-t border-white/10 pt-3">
                <span class="text-white font-semibold">Total</span>
                <span class="text-blue-400 font-bold text-lg">{{ number_format($stats['revenu_mois_total'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-xs text-white/30 pt-1 border-t border-white/5">
                <span>{{ $stats['reservations_mois'] }} réservation(s)</span>
                <span>{{ $stats['commandes_mois'] }} commande(s)</span>
            </div>
        </div>
    </div>

    {{-- Cette année --}}
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5">
        <p class="text-green-400 text-xs uppercase tracking-widest font-bold mb-4">Cette année</p>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-white/50">🛏️ Chambres</span>
                <span class="text-white font-medium">{{ number_format($stats['revenu_annee_chambre'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">🍽️ Restaurant</span>
                <span class="text-white font-medium">{{ number_format($stats['revenu_annee_restaurant'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm border-t border-white/10 pt-3">
                <span class="text-white font-semibold">Total</span>
                <span class="text-green-400 font-bold text-lg">{{ number_format($stats['revenu_annee_total'], 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>
</div>

{{-- ÉVOLUTION 7 JOURS --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="text-white font-semibold mb-6">Évolution sur 7 jours</h3>
    @php
        $maxVal = $evolution->max(fn($e) => $e['chambres'] + $e['restaurant']) ?: 1;
    @endphp
    <div class="space-y-3">
        @foreach($evolution as $jour)
        @php $total = $jour['chambres'] + $jour['restaurant']; @endphp
        <div class="flex items-center gap-4">
            <div class="w-16 text-right">
                <p class="text-white/60 text-xs">{{ $jour['date'] }}</p>
                <p class="text-white/30 text-xs capitalize">{{ mb_substr($jour['jour'], 0, 3) }}</p>
            </div>
            <div class="flex-1 space-y-1">
                {{-- Barre chambres --}}
                @if($jour['chambres'] > 0)
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-amber-400/60 rounded-full transition-all"
                             style="width: {{ ($jour['chambres'] / $maxVal) * 100 }}%"></div>
                    </div>
                    <span class="text-xs text-amber-400/70 w-28 text-right">{{ number_format($jour['chambres'], 0, ',', ' ') }} Ar</span>
                </div>
                @endif
                {{-- Barre restaurant --}}
                @if($jour['restaurant'] > 0)
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-white/5 rounded-full h-2 overflow-hidden">
                        <div class="h-full bg-blue-400/60 rounded-full transition-all"
                             style="width: {{ ($jour['restaurant'] / $maxVal) * 100 }}%"></div>
                    </div>
                    <span class="text-xs text-blue-400/70 w-28 text-right">{{ number_format($jour['restaurant'], 0, ',', ' ') }} Ar</span>
                </div>
                @endif
                @if($jour['chambres'] === 0 && $jour['restaurant'] === 0)
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-white/5 rounded-full h-2"></div>
                    <span class="text-xs text-white/20 w-28 text-right">Aucun revenu</span>
                </div>
                @endif
            </div>
            <div class="w-28 text-right">
                <p class="text-white font-semibold text-sm">{{ number_format($total, 0, ',', ' ') }} Ar</p>
            </div>
        </div>
        @endforeach
    </div>
    {{-- Légende --}}
    <div class="flex items-center gap-6 mt-4 pt-4 border-t border-white/10">
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

{{-- TOP PLATS + TOP CHAMBRES --}}
<div class="grid md:grid-cols-2 gap-6">

    {{-- Top 5 plats --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-5">🍽️ Top 5 plats ce mois</h3>
        @forelse($topPlats as $index => $item)
        <div class="flex items-center gap-3 mb-4">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                        {{ $index === 0 ? 'bg-amber-400 text-black' : 'bg-white/10 text-white/60' }}">
                {{ $index + 1 }}
            </div>
            <div class="flex-grow">
                <p class="text-white text-sm font-medium">{{ $item->menu?->nom ?? '—' }}</p>
                <div class="w-full bg-white/5 rounded-full h-1.5 mt-1.5 overflow-hidden">
                    @php $maxQte = $topPlats->max('total_commande') ?: 1; @endphp
                    <div class="h-full bg-amber-400/50 rounded-full"
                         style="width: {{ ($item->total_commande / $maxQte) * 100 }}%"></div>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-amber-400 text-sm font-bold">× {{ $item->total_commande }}</p>
                <p class="text-white/30 text-xs">{{ number_format($item->total_revenu, 0, ',', ' ') }} Ar</p>
            </div>
        </div>
        @empty
        <p class="text-white/40 text-sm text-center py-4">Aucune donnée ce mois.</p>
        @endforelse
    </div>

    {{-- Top chambres --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-5">🛏️ Top chambres ce mois</h3>
        @forelse($topChambres as $index => $item)
        <div class="flex items-center gap-3 mb-4">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0
                        {{ $index === 0 ? 'bg-amber-400 text-black' : 'bg-white/10 text-white/60' }}">
                {{ $index + 1 }}
            </div>
            <div class="flex-grow">
                <p class="text-white text-sm font-medium">{{ $item->chambre?->numero_chambre ?? '—' }}</p>
                <p class="text-white/40 text-xs capitalize">{{ $item->chambre?->type_chambre }}</p>
                <div class="w-full bg-white/5 rounded-full h-1.5 mt-1.5 overflow-hidden">
                    @php $maxNb = $topChambres->max('nb_reservations') ?: 1; @endphp
                    <div class="h-full bg-blue-400/50 rounded-full"
                         style="width: {{ ($item->nb_reservations / $maxNb) * 100 }}%"></div>
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-blue-400 text-sm font-bold">{{ $item->nb_reservations }} rés.</p>
                <p class="text-white/30 text-xs">{{ number_format($item->total_revenu, 0, ',', ' ') }} Ar</p>
            </div>
        </div>
        @empty
        <p class="text-white/40 text-sm text-center py-4">Aucune donnée ce mois.</p>
        @endforelse
    </div>
</div>

@endsection