@extends('layouts.dashboard')
@section('title', 'Rapport Quotidien')
@include('components.nav-gerant')
@section('content')

{{-- EN-TÊTE + SÉLECTEUR DE DATE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Rapport Quotidien</h2>
        <p class="text-white/50 text-sm mt-1">
            {{ $date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
        </p>
    </div>
    <form method="GET" action="{{ route('gerant.statistiques.rapport') }}" class="flex items-center gap-3">
        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}"
               class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm
                      focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
        <button type="submit"
                class="px-5 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">
            Voir
        </button>
    </form>
</div>

{{-- CARTES RÉSUMÉ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Réservations</p>
        <p class="text-3xl font-bold text-amber-400">{{ $rapport['nb_reservations'] }}</p>
        <div class="mt-3 space-y-1 text-xs">
            <div class="flex justify-between text-white/40">
                <span>En attente</span><span class="text-amber-400">{{ $rapport['reservations_en_attente'] }}</span>
            </div>
            <div class="flex justify-between text-white/40">
                <span>Confirmées</span><span class="text-blue-400">{{ $rapport['reservations_confirmees'] }}</span>
            </div>
            <div class="flex justify-between text-white/40">
                <span>Payées</span><span class="text-green-400">{{ $rapport['reservations_payees'] }}</span>
            </div>
        </div>
    </div>
    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Commandes</p>
        <p class="text-3xl font-bold text-blue-400">{{ $rapport['nb_commandes'] }}</p>
        <div class="mt-3 space-y-1 text-xs">
            <div class="flex justify-between text-white/40">
                <span>En attente</span><span class="text-amber-400">{{ $rapport['commandes_en_attente'] }}</span>
            </div>
            <div class="flex justify-between text-white/40">
                <span>En préparation</span><span class="text-blue-400">{{ $rapport['commandes_en_prep'] }}</span>
            </div>
            <div class="flex justify-between text-white/40">
                <span>Livrées</span><span class="text-green-400">{{ $rapport['commandes_livrees'] }}</span>
            </div>
        </div>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Revenus</p>
        <p class="text-2xl font-bold text-green-400">{{ number_format($rapport['revenu_total'], 0, ',', ' ') }}</p>
        <p class="text-white/30 text-xs mb-3">Ariary</p>
        <div class="space-y-1 text-xs">
            <div class="flex justify-between text-white/40">
                <span>🛏️ Chambres</span><span>{{ number_format($rapport['revenu_chambres'], 0, ',', ' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-white/40">
                <span>🍽️ Restaurant</span><span>{{ number_format($rapport['revenu_restaurant'], 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Occupation</p>
        <p class="text-3xl font-bold text-white">{{ $rapport['chambres_occupees'] }}/{{ $rapport['chambres_total'] }}</p>
        <p class="text-white/30 text-xs mb-3">chambres occupées</p>
        @php $taux = $rapport['chambres_total'] > 0 ? round(($rapport['chambres_occupees'] / $rapport['chambres_total']) * 100) : 0; @endphp
        <div class="w-full bg-white/10 rounded-full h-2">
            <div class="h-2 rounded-full bg-gradient-to-r from-amber-400 to-amber-300"
                 style="width: {{ $taux }}%"></div>
        </div>
        <p class="text-white/40 text-xs mt-1">Taux : {{ $taux }}%</p>
    </div>
</div>

{{-- RÉSERVATIONS DU JOUR --}}
<div class="grid lg:grid-cols-2 gap-6">

    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white font-semibold">🛏️ Réservations du jour</h3>
            <span class="text-white/40 text-xs">{{ $reservations->count() }}</span>
        </div>
        @forelse($reservations->take(8) as $res)
        <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between hover:bg-white/5 transition">
            <div>
                <p class="text-white text-sm font-medium">{{ $res->nom }}</p>
                <p class="text-white/40 text-xs">
                    {{ $res->chambre?->numero_chambre ?? '—' }}
                    @if($res->date_reservation)
                        · {{ $res->date_reservation->format('d/m') }}
                    @elseif($res->date_arrivee)
                        · {{ $res->date_arrivee->format('d/m') }} → {{ $res->date_depart?->format('d/m') }}
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-amber-400 text-xs font-semibold">{{ number_format($res->prix_total, 0, ',', ' ') }} Ar</span>
                <span class="px-2.5 py-1 rounded-full text-xs border {{ $res->couleurStatut() }}">{{ $res->libelleStatut() }}</span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-white/40 text-sm">Aucune réservation ce jour.</div>
        @endforelse
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
            <h3 class="text-white font-semibold">🍽️ Commandes du jour</h3>
            <span class="text-white/40 text-xs">{{ $commandes->count() }}</span>
        </div>
        @forelse($commandes->take(8) as $cmd)
        <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between hover:bg-white/5 transition">
            <div>
                <p class="text-white text-sm font-medium">{{ $cmd->nom }}</p>
                <p class="text-white/40 text-xs">
                    {{ $cmd->items->count() }} plat(s)
                    · {{ $cmd->created_at->format('H:i') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-amber-400 text-xs font-semibold">{{ number_format($cmd->total, 0, ',', ' ') }} Ar</span>
                <span class="px-2.5 py-1 rounded-full text-xs border {{ $cmd->couleurStatut() }}">{{ $cmd->iconeStatut() }} {{ $cmd->libelleStatut() }}</span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-white/40 text-sm">Aucune commande ce jour.</div>
        @endforelse
    </div>
</div>

@endsection