{{-- resources/views/client/reservations/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Mes Réservations')

@include('components.nav-client')

@section('content')

{{-- EN-TÊTE --}}
<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-wide">Mes Réservations</h2>
    <p class="text-white/50 text-sm mt-1">Historique et suivi de toutes vos réservations</p>
</div>

{{-- FLASH --}}
@if (session('success'))
    <div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
        <span>✅</span> {{ session('success') }}
    </div>
@endif

{{-- STATS RAPIDES --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
        $stats = [
            'en_attente' => $reservations->where('statut', 'en_attente')->count(),
            'confirmee'  => $reservations->where('statut', 'confirmee')->count(),
            'payee'      => $reservations->where('statut', 'payee')->count(),
            'terminee'   => $reservations->where('statut', 'terminee')->count(),
        ];
    @endphp
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-amber-400">{{ $stats['en_attente'] }}</p>
        <p class="text-white/50 text-xs mt-1">En attente</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-blue-400">{{ $stats['confirmee'] }}</p>
        <p class="text-white/50 text-xs mt-1">Confirmées</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-green-400">{{ $stats['payee'] }}</p>
        <p class="text-white/50 text-xs mt-1">Payées</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-neutral-400">{{ $stats['terminee'] }}</p>
        <p class="text-white/50 text-xs mt-1">Terminées</p>
    </div>
</div>

{{-- LISTE DES RÉSERVATIONS --}}
@if ($reservations->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
        <p class="text-5xl mb-4">📅</p>
        <p class="text-white/60 text-lg">Vous n'avez aucune réservation pour le moment.</p>
        <a href="{{ route('client.chambres.index') }}"
           class="mt-4 inline-block text-amber-400 hover:text-amber-300 transition text-sm">
            Voir nos chambres →
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach ($reservations as $reservation)
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden
                    hover:border-white/20 transition">

            <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">

                {{-- Infos chambre + dates --}}
                <div class="flex items-start gap-4">

                    {{-- Image mini --}}
                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                        @if ($reservation->chambre?->image)
                            <img src="{{ Storage::url($reservation->chambre->image) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-2xl text-white/20">🛏️</div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-white font-semibold">
                                {{ $reservation->chambre?->numero_chambre ?? 'Chambre supprimée' }}
                            </p>
                            <span class="text-white/30 text-xs">·</span>
                            <p class="text-white/50 text-sm capitalize">
                                {{ $reservation->chambre?->type_chambre }}
                            </p>
                        </div>

                        {{-- Dates --}}
                        <p class="text-white/60 text-sm">
                            @if ($reservation->date_reservation)
                                📅 {{ $reservation->date_reservation->format('d/m/Y') }}
                                <span class="text-white/30 ml-1">(1 nuit)</span>
                            @else
                                📅 {{ $reservation->date_arrivee?->format('d/m/Y') }}
                                → {{ $reservation->date_depart?->format('d/m/Y') }}
                                <span class="text-white/30 ml-1">({{ $reservation->nombreNuits() }} nuits)</span>
                            @endif
                        </p>

                        <p class="text-white/40 text-xs mt-1">
                            Réservé le {{ $reservation->created_at->format('d/m/Y à H:i') }}
                            · <span class="text-amber-400/70">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </p>
                    </div>
                </div>

                {{-- Prix + Statut + Actions --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 md:flex-shrink-0">

                    <div class="text-right">
                        <p class="text-amber-400 font-bold text-xl">
                            {{ number_format($reservation->prix_total, 0, ',', ' ') }}
                            <span class="text-xs font-normal text-white/40">Ar</span>
                        </p>
                    </div>

                    {{-- Badge statut --}}
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold border
                                 {{ $reservation->couleurStatut() }}">
                        {{ $reservation->libelleStatut() }}
                    </span>

                    {{-- Bouton annuler (seulement si en_attente) --}}
                    @if ($reservation->statut === 'en_attente')
                        <form method="POST"
                              action="{{ route('client.reservations.annuler', $reservation) }}"
                              onsubmit="return confirm('Annuler cette réservation ?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-4 py-1.5 border border-red-500/30 text-red-400 rounded-full text-xs
                                           hover:bg-red-500/10 transition">
                                Annuler
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
