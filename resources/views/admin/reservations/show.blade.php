@extends('layouts.dashboard')
@section('title', 'Réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT))
@include('components.nav-admin')
@section('content')

{{-- EN-TÊTE --}}
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('admin.reservations.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div>
        <h2 class="text-2xl font-bold text-white">
            Réservation <span class="text-amber-400">#{{ str_pad($reservation->id, 6, '0', STR_PAD_LEFT) }}</span>
        </h2>
        <p class="text-white/50 text-sm mt-1">Créée le {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="ml-auto flex items-center gap-3">
        <span class="px-4 py-2 rounded-full text-sm font-semibold border {{ $reservation->couleurStatut() }}">
            {{ $reservation->libelleStatut() }}
        </span>
        {{-- Lien vers le gérant pour agir --}}
        <a href="{{ route('gerant.reservations.show', $reservation) }}"
           class="px-4 py-2 bg-amber-400/10 border border-amber-400/20 text-amber-400
                  rounded-full text-xs font-semibold hover:bg-amber-400/20 transition">
            ✏️ Gérer (Gérant)
        </a>
    </div>
</div>

{{-- BANNIÈRE LECTURE SEULE --}}
<div class="flex items-center gap-3 p-4 bg-blue-400/5 border border-blue-400/20 rounded-xl mb-6">
    <span class="text-blue-400">ℹ️</span>
    <p class="text-blue-400/80 text-sm">
        Cette vue est en <strong>lecture seule</strong>. Les modifications de statut sont réservées au gérant.
    </p>
</div>

<div class="grid lg:grid-cols-2 gap-6">

    {{-- Infos client --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-4">👤 Client</h3>
        <div class="space-y-3">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Nom</p>
                <p class="text-white font-medium">{{ $reservation->nom }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Email</p>
                <p class="text-white">{{ $reservation->email }}</p>
            </div>
            @if($reservation->user)
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Compte</p>
                <a href="{{ route('admin.users.edit', $reservation->user) }}"
                   class="text-amber-400 text-sm hover:text-amber-300 transition">
                    Voir le profil utilisateur →
                </a>
            </div>
            @endif
            @if($reservation->motif)
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Motif</p>
                <p class="text-white/70">{{ $reservation->motif }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Infos chambre & séjour --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-4">🛏️ Séjour</h3>
        <div class="space-y-3">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Chambre</p>
                <p class="text-white font-medium">{{ $reservation->chambre?->numero_chambre ?? '—' }}</p>
                <p class="text-white/50 text-xs capitalize">{{ $reservation->chambre?->type_chambre }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Période</p>
                @if($reservation->date_reservation)
                    <p class="text-white">{{ $reservation->date_reservation->format('d/m/Y') }}</p>
                    <p class="text-white/40 text-xs">1 nuit</p>
                @else
                    <p class="text-white">
                        {{ $reservation->date_arrivee?->format('d/m/Y') }}
                        → {{ $reservation->date_depart?->format('d/m/Y') }}
                    </p>
                    <p class="text-white/40 text-xs">{{ $reservation->nombreNuits() }} nuit(s)</p>
                @endif
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Prix par nuit</p>
                <p class="text-white">{{ number_format($reservation->chambre?->prix_nuit ?? 0, 0, ',', ' ') }} Ar</p>
            </div>
            <div class="border-t border-white/10 pt-3">
                <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Montant total</p>
                <p class="text-amber-400 font-bold text-2xl">
                    {{ number_format($reservation->prix_total, 0, ',', ' ') }}
                    <span class="text-sm font-normal text-white/40">Ar</span>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection