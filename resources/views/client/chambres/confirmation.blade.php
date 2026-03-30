{{-- resources/views/client/chambres/confirmation.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Réservation confirmée')

@include('components.nav-client')

@section('content')

<div class="max-w-2xl mx-auto">

    {{-- ICÔNE SUCCÈS --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full
                    bg-gradient-to-br from-amber-400/20 to-amber-300/10
                    border border-amber-400/30 mb-6">
            <span class="text-4xl">✅</span>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Réservation enregistrée !</h2>
        <p class="text-white/50">Votre demande a bien été prise en compte. Elle est en attente de confirmation.</p>
    </div>

    {{-- CARTE RÉCAPITULATIF --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">

        {{-- En-tête de la carte --}}
        <div class="bg-gradient-to-r from-amber-400/10 to-amber-300/5 border-b border-white/10 px-6 py-4
                    flex justify-between items-center">
            <div>
                <p class="text-white/50 text-xs uppercase tracking-widest">Numéro de réservation</p>
                <p class="text-amber-400 font-bold text-lg">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold border
                         bg-amber-400/10 text-amber-400 border-amber-400/20">
                En attente
            </span>
        </div>

        {{-- Détails --}}
        <div class="p-6 grid md:grid-cols-2 gap-6">

            <div class="space-y-4">
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Chambre</p>
                    <p class="text-white font-semibold">{{ $reservation->chambre->numero_chambre }}</p>
                    <p class="text-white/50 text-sm capitalize">{{ $reservation->chambre->type_chambre }}</p>
                </div>

                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Client</p>
                    <p class="text-white font-semibold">{{ $reservation->nom }}</p>
                    <p class="text-white/50 text-sm">{{ $reservation->email }}</p>
                </div>

                @if ($reservation->motif)
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Motif</p>
                    <p class="text-white/70 text-sm">{{ $reservation->motif }}</p>
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">
                        {{ $reservation->date_reservation ? 'Date du séjour' : 'Période du séjour' }}
                    </p>
                    @if ($reservation->date_reservation)
                        <p class="text-white font-semibold">
                            {{ $reservation->date_reservation->translatedFormat('l d F Y') }}
                        </p>
                        <p class="text-white/50 text-sm">1 nuit</p>
                    @else
                        <p class="text-white font-semibold">
                            {{ $reservation->date_arrivee->format('d/m/Y') }}
                            → {{ $reservation->date_depart->format('d/m/Y') }}
                        </p>
                        <p class="text-white/50 text-sm">{{ $reservation->nombreNuits() }} nuit(s)</p>
                    @endif
                </div>

                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Montant total</p>
                    <p class="text-amber-400 font-bold text-2xl">
                        {{ number_format($reservation->prix_total, 0, ',', ' ') }}
                        <span class="text-sm font-normal text-white/40">Ar</span>
                    </p>
                </div>

                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Date de la demande</p>
                    <p class="text-white/70 text-sm">{{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- NOTE PAIEMENT --}}
    <div class="p-5 bg-amber-400/5 border border-amber-400/20 rounded-2xl mb-8 flex items-start gap-3">
        <span class="text-amber-400 text-2xl mt-0.5">💳</span>
        <div>
            <p class="text-amber-400 font-semibold text-sm">Paiement à l'hôtel</p>
            <p class="text-white/60 text-sm mt-1 leading-relaxed">
                Le paiement de <strong class="text-white">{{ number_format($reservation->prix_total, 0, ',', ' ') }} Ar</strong>
                s'effectuera directement à la réception de l'hôtel MISALO, à votre arrivée ou après votre séjour.
                Présentez simplement votre numéro de réservation <strong class="text-amber-400">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</strong>.
            </p>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('client.reservations.index') }}"
           class="flex items-center justify-center gap-2 px-8 py-3
                  bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                  rounded-full shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all duration-300 text-sm">
            📅 Voir mes réservations
        </a>
        <a href="{{ route('client.chambres.index') }}"
           class="flex items-center justify-center gap-2 px-8 py-3
                  border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            🛏️ Voir toutes les chambres
        </a>
    </div>

</div>

@endsection
