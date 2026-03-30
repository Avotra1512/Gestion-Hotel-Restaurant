@extends('layouts.dashboard')
@section('title', 'Profil — ' . $user->name)
@include('components.nav-gerant')
@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('gerant.clients.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10 text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-amber-400/10 border border-amber-400/30 flex items-center justify-center text-2xl font-bold text-amber-400">
            {{ strtoupper(substr($user->name,0,1)) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
            <p class="text-white/50 text-sm">{{ $user->email }} · Client depuis le {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
        <p class="text-white/50 text-xs mt-1">Total</p>
    </div>
    <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-amber-400">{{ $stats['en_attente'] }}</p>
        <p class="text-white/50 text-xs mt-1">En attente</p>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-4 text-center">
        <p class="text-2xl font-bold text-green-400">{{ $stats['payees'] }}</p>
        <p class="text-white/50 text-xs mt-1">Payées</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-center">
        <p class="text-xl font-bold text-amber-400">{{ number_format($stats['revenus'],0,',',' ') }}</p>
        <p class="text-white/50 text-xs mt-1">Ar dépensés</p>
    </div>
</div>

<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-white/10">
        <h3 class="text-white font-semibold">Historique des réservations</h3>
    </div>
    @forelse($reservations as $res)
    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-white/5 transition border-b border-white/5">
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
                    <span class="text-amber-400/70 font-mono text-xs ml-2">#{{ str_pad($res->id,6,'0',STR_PAD_LEFT) }}</span>
                </p>
                <p class="text-white/40 text-xs">
                    @if($res->date_reservation)
                        {{ $res->date_reservation->format('d/m/Y') }} · 1 nuit
                    @else
                        {{ $res->date_arrivee?->format('d/m/Y') }} → {{ $res->date_depart?->format('d/m/Y') }} · {{ $res->nombreNuits() }} nuits
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-amber-400 font-semibold text-sm">{{ number_format($res->prix_total,0,',',' ') }} Ar</span>
            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $res->couleurStatut() }}">{{ $res->libelleStatut() }}</span>
            <a href="{{ route('gerant.reservations.show', $res) }}"
               class="px-3 py-1.5 border border-white/10 text-white/50 rounded-full text-xs hover:text-white hover:bg-white/5 transition">Voir</a>
        </div>
    </div>
    @empty
    <div class="p-12 text-center text-white/40">Aucune réservation pour ce client.</div>
    @endforelse
</div>

@endsection