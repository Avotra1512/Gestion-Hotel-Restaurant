@extends('layouts.dashboard')
@section('title', 'Mes Factures')
@include('components.nav-client')
@section('content')

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Mes Factures</h2>
        <p class="text-white/50 text-sm mt-1">Téléchargez vos factures en PDF</p>
    </div>

    {{-- Facture groupée --}}
    @if($reservations->count() > 0 || $commandes->count() > 0)
    <a href="{{ route('client.factures.groupee') }}"
       class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-400 to-amber-300
              text-black font-semibold rounded-full shadow-lg shadow-amber-400/20
              hover:scale-[1.03] transition-all text-sm">
        📥 Tout télécharger (PDF groupé)
    </a>
    @endif
</div>

{{-- RÉSUMÉ FINANCIER --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total chambres</p>
        <p class="text-2xl font-bold text-amber-400">
            {{ number_format($totalReservations, 0, ',', ' ') }}
            <span class="text-sm font-normal text-white/40">Ar</span>
        </p>
        <p class="text-white/30 text-xs mt-1">{{ $reservations->count() }} facture(s)</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total restaurant</p>
        <p class="text-2xl font-bold text-blue-400">
            {{ number_format($totalCommandes, 0, ',', ' ') }}
            <span class="text-sm font-normal text-white/40">Ar</span>
        </p>
        <p class="text-white/30 text-xs mt-1">{{ $commandes->count() }} facture(s)</p>
    </div>
    <div class="bg-gradient-to-br from-amber-400/15 to-amber-300/5
                border border-amber-400/30 rounded-2xl p-5">
        <p class="text-amber-400/70 text-xs uppercase tracking-widest mb-2">Total général</p>
        <p class="text-2xl font-bold text-white">
            {{ number_format($totalGeneral, 0, ',', ' ') }}
            <span class="text-sm font-normal text-amber-400/50">Ar</span>
        </p>
        <p class="text-white/30 text-xs mt-1">{{ $reservations->count() + $commandes->count() }} facture(s)</p>
    </div>
</div>

{{-- SECTION : Factures Chambres --}}
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <span class="text-xl">🛏️</span>
        <h3 class="text-white font-bold text-lg">Réservations</h3>
        <span class="text-white/30 text-xs">{{ $reservations->count() }} facture(s)</span>
        <div class="flex-1 h-px bg-white/5 ml-2"></div>
    </div>

    @if($reservations->isEmpty())
        <div class="bg-white/5 border border-white/10 rounded-2xl p-10 text-center">
            <p class="text-3xl mb-3">🛏️</p>
            <p class="text-white/40 text-sm">Aucune facture de réservation disponible.</p>
            <p class="text-white/30 text-xs mt-1">Les factures apparaissent une fois le paiement validé.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($reservations as $res)
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5
                        hover:border-amber-400/20 transition flex flex-col sm:flex-row sm:items-center gap-4">

                {{-- Icône + Infos --}}
                <div class="flex items-center gap-4 flex-grow">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                        @if($res->chambre?->image)
                            <img src="{{ Storage::url($res->chambre->image) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl text-white/20">🛏️</div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-white font-semibold">{{ $res->chambre?->numero_chambre ?? '—' }}</p>
                            <span class="text-white/30">·</span>
                            <span class="text-amber-400/70 font-mono text-xs">
                                #{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <p class="text-white/40 text-xs">
                            @if($res->date_reservation)
                                {{ $res->date_reservation->format('d/m/Y') }} · 1 nuit
                            @else
                                {{ $res->date_arrivee?->format('d/m/Y') }}
                                → {{ $res->date_depart?->format('d/m/Y') }}
                                · {{ $res->nombreNuits() }} nuits
                            @endif
                        </p>
                        <p class="text-white/30 text-xs mt-0.5">
                            Payée le {{ $res->updated_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                {{-- Montant + Statut + Bouton --}}
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-amber-400 font-bold text-lg">
                            {{ number_format($res->prix_total, 0, ',', ' ') }} Ar
                        </p>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $res->couleurStatut() }}">
                            {{ $res->libelleStatut() }}
                        </span>
                    </div>
                    <a href="{{ route('client.factures.reservation', $res) }}"
                       class="flex items-center gap-2 px-5 py-2.5
                              bg-amber-400/10 border border-amber-400/20 text-amber-400
                              rounded-full text-sm font-semibold
                              hover:bg-amber-400 hover:text-black transition-all duration-200">
                        <span>📥</span>
                        <span>PDF</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- SECTION : Factures Restaurant --}}
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <span class="text-xl">🍽️</span>
        <h3 class="text-white font-bold text-lg">Commandes Restaurant</h3>
        <span class="text-white/30 text-xs">{{ $commandes->count() }} facture(s)</span>
        <div class="flex-1 h-px bg-white/5 ml-2"></div>
    </div>

    @if($commandes->isEmpty())
        <div class="bg-white/5 border border-white/10 rounded-2xl p-10 text-center">
            <p class="text-3xl mb-3">🍽️</p>
            <p class="text-white/40 text-sm">Aucune facture de commande disponible.</p>
            <p class="text-white/30 text-xs mt-1">Les factures apparaissent une fois la commande livrée.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($commandes as $cmd)
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5
                        hover:border-amber-400/20 transition flex flex-col sm:flex-row sm:items-center gap-4">

                {{-- Infos commande --}}
                <div class="flex items-center gap-4 flex-grow">
                    <div class="w-12 h-12 rounded-full bg-amber-400/10 border border-amber-400/20
                                flex items-center justify-center text-xl flex-shrink-0">
                        🍽️
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-white font-semibold">Commande restaurant</p>
                            <span class="text-amber-400/70 font-mono text-xs">
                                #{{ str_pad($cmd->id, 6, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-1 mb-1">
                            @foreach($cmd->items->take(3) as $item)
                            <span class="text-white/40 text-xs">{{ $item->menu?->nom ?? '—' }}{{ !$loop->last ? ',' : '' }}</span>
                            @endforeach
                            @if($cmd->items->count() > 3)
                            <span class="text-white/30 text-xs">+{{ $cmd->items->count() - 3 }} autres</span>
                            @endif
                        </div>
                        <p class="text-white/30 text-xs">
                            {{ $cmd->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>

                {{-- Montant + Bouton --}}
                <div class="flex items-center gap-4 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-amber-400 font-bold text-lg">
                            {{ number_format($cmd->total, 0, ',', ' ') }} Ar
                        </p>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $cmd->couleurStatut() }}">
                            {{ $cmd->libelleStatut() }}
                        </span>
                    </div>
                    <a href="{{ route('client.factures.commande', $cmd) }}"
                       class="flex items-center gap-2 px-5 py-2.5
                              bg-amber-400/10 border border-amber-400/20 text-amber-400
                              rounded-full text-sm font-semibold
                              hover:bg-amber-400 hover:text-black transition-all duration-200">
                        <span>📥</span>
                        <span>PDF</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@endsection