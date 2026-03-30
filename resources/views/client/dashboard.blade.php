@extends('layouts.dashboard')

@section('title', 'Mon Espace Client')

@include('components.nav-client')


@section('content')

{{-- BIENVENUE --}}
<div class="bg-gradient-to-br from-white/10 to-transparent p-8 rounded-3xl border border-white/10 mb-8">
    <h3 class="text-white/50 text-sm uppercase tracking-widest">Bienvenue,</h3>
    <p class="text-3xl font-bold text-white mt-1">{{ Auth::user()->name }}</p>
    <p class="text-white/50 text-sm mt-2">{{ Auth::user()->email }}</p>
    <a href="{{ route('client.reservations.index') }}"
       class="inline-flex items-center gap-2 mt-5 text-amber-400 hover:text-amber-300 transition text-sm font-medium">
        Consulter mes réservations en cours →
    </a>
</div>

{{-- STATISTIQUES --}}
@php
    $reservations = \App\Models\ReservationChambre::where('user_id', Auth::id())->get();
    $stats = [
        'total'      => $reservations->count(),
        'en_attente' => $reservations->where('statut', 'en_attente')->count(),
        'confirmees' => $reservations->where('statut', 'confirmee')->count(),
        'payees'     => $reservations->where('statut', 'payee')->count(),
        'terminee'   => $reservations->where('statut', 'terminee')->count(),
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-amber-400/30 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Total</p>
        <p class="text-3xl font-bold text-white">{{ $stats['total'] }}</p>
        <p class="text-white/30 text-xs mt-2">réservations</p>
    </div>
    <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-5 hover:border-amber-400/40 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">En attente</p>
        <p class="text-3xl font-bold text-amber-400">{{ $stats['en_attente'] }}</p>
        <p class="text-white/30 text-xs mt-2">en cours</p>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5 hover:border-green-400/40 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Payées</p>
        <p class="text-3xl font-bold text-green-400">{{ $stats['payees'] }}</p>
        <p class="text-white/30 text-xs mt-2">validées</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 hover:border-white/20 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Terminées</p>
        <p class="text-3xl font-bold text-neutral-400">{{ $stats['terminee'] }}</p>
        <p class="text-white/30 text-xs mt-2">séjours passés</p>
    </div>
</div>

{{-- DERNIÈRES RÉSERVATIONS --}}
@php
    $dernieres = \App\Models\ReservationChambre::with('chambre')
        ->where('user_id', Auth::id())
        ->latest()
        ->take(5)
        ->get();
@endphp

<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
        <h3 class="text-white font-semibold">Mes dernières réservations</h3>
        <a href="{{ route('client.reservations.index') }}"
           class="text-amber-400 text-sm hover:text-amber-300 transition">Voir tout →</a>
    </div>

    @if($dernieres->isEmpty())
        <div class="p-12 text-center">
            <p class="text-4xl mb-3">🛏️</p>
            <p class="text-white/50 text-sm">Vous n'avez pas encore de réservation.</p>
            <a href="{{ route('client.chambres.index') }}"
               class="mt-4 inline-block text-amber-400 hover:text-amber-300 transition text-sm">
                Découvrir nos chambres →
            </a>
        </div>
    @else
        <div class="divide-y divide-white/5">
            @foreach($dernieres as $res)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-white/5 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                        @if($res->chambre?->image)
                            <img src="{{ Storage::url($res->chambre->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-lg text-white/20">🛏️</div>
                        @endif
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">
                            {{ $res->chambre?->numero_chambre ?? '—' }}
                            <span class="text-white/30 mx-1">·</span>
                            <span class="text-amber-400/60 font-mono text-xs">#{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </p>
                        <p class="text-white/40 text-xs">
                            @if($res->date_reservation)
                                {{ $res->date_reservation->format('d/m/Y') }} · 1 nuit
                            @else
                                {{ $res->date_arrivee?->format('d/m/Y') }}
                                → {{ $res->date_depart?->format('d/m/Y') }}
                                · {{ $res->nombreNuits() }} nuits
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <p class="text-amber-400 font-semibold text-sm hidden sm:block">
                        {{ number_format($res->prix_total, 0, ',', ' ') }} Ar
                    </p>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $res->couleurStatut() }}">
                        {{ $res->libelleStatut() }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ACCÈS RAPIDES --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <a href="{{ route('client.chambres.index') }}"
       class="flex items-center gap-4 p-6 bg-white/5 border border-white/10 rounded-2xl
              hover:border-amber-400/30 hover:-translate-y-1 transition-all duration-300 group">
        <span class="text-3xl">🛏️</span>
        <div>
            <p class="text-white font-semibold group-hover:text-amber-400 transition">Voir nos chambres</p>
            <p class="text-white/40 text-xs mt-1">Parcourir le catalogue et réserver</p>
        </div>
        <span class="ml-auto text-white/30 group-hover:text-amber-400 transition">→</span>
    </a>
    <a href="{{ route('client.reservations.index') }}"
       class="flex items-center gap-4 p-6 bg-white/5 border border-white/10 rounded-2xl
              hover:border-amber-400/30 hover:-translate-y-1 transition-all duration-300 group">
        <span class="text-3xl">📅</span>
        <div>
            <p class="text-white font-semibold group-hover:text-amber-400 transition">Mes réservations</p>
            <p class="text-white/40 text-xs mt-1">Suivre et gérer vos séjours</p>
        </div>
        <span class="ml-auto text-white/30 group-hover:text-amber-400 transition">→</span>
    </a>
</div>

@endsection