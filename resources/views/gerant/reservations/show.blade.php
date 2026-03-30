@extends('layouts.dashboard')
@section('title', 'Réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT))
@include('components.nav-gerant')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('gerant.reservations.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10 text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div>
        <h2 class="text-2xl font-bold text-white">Réservation <span class="text-amber-400">#{{ str_pad($reservation->id,6,'0',STR_PAD_LEFT) }}</span></h2>
        <p class="text-white/50 text-sm mt-1">Créée le {{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="ml-auto">
        <span class="px-4 py-2 rounded-full text-sm font-semibold border {{ $reservation->couleurStatut() }}">{{ $reservation->libelleStatut() }}</span>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">

        {{-- Client --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4">👤 Informations client</h3>
            <div class="grid md:grid-cols-2 gap-4">
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
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Profil client</p>
                    <a href="{{ route('gerant.clients.show', $reservation->user) }}" class="text-amber-400 hover:text-amber-300 text-sm transition">Voir le profil →</a>
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

        {{-- Chambre & Dates --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4">🛏️ Chambre & Dates</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Chambre</p>
                    <p class="text-white font-medium">{{ $reservation->chambre?->numero_chambre ?? '—' }}</p>
                    <p class="text-white/50 text-sm capitalize">{{ $reservation->chambre?->type_chambre }}</p>
                </div>
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Prix par nuit</p>
                    <p class="text-amber-400 font-semibold">{{ number_format($reservation->chambre?->prix_nuit ?? 0,0,',',' ') }} Ar</p>
                </div>
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Période</p>
                    @if($reservation->date_reservation)
                        <p class="text-white">{{ $reservation->date_reservation->format('d/m/Y') }}</p>
                        <p class="text-white/40 text-xs">1 nuit</p>
                    @else
                        <p class="text-white">{{ $reservation->date_arrivee?->format('d/m/Y') }} → {{ $reservation->date_depart?->format('d/m/Y') }}</p>
                        <p class="text-white/40 text-xs">{{ $reservation->nombreNuits() }} nuit(s)</p>
                    @endif
                </div>
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Montant total</p>
                    <p class="text-2xl font-bold text-amber-400">{{ number_format($reservation->prix_total,0,',',' ') }} <span class="text-sm font-normal text-white/40">Ar</span></p>
                </div>
            </div>
        </div>

        {{-- Modifier statut --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4">🔄 Modifier le statut</h3>
            <form method="POST" action="{{ route('gerant.reservations.statut', $reservation) }}" class="flex flex-wrap items-end gap-4">
                @csrf @method('PATCH')
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Nouveau statut</label>
                    <select name="statut" class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition">
                        <option value="en_attente" {{ $reservation->statut==='en_attente' ? 'selected' : '' }}>⏳ En attente</option>
                        <option value="confirmee"  {{ $reservation->statut==='confirmee'  ? 'selected' : '' }}>✅ Confirmée</option>
                        <option value="payee"      {{ $reservation->statut==='payee'      ? 'selected' : '' }}>💰 Payée</option>
                        <option value="terminee"   {{ $reservation->statut==='terminee'   ? 'selected' : '' }}>🏁 Terminée</option>
                        <option value="annulee"    {{ $reservation->statut==='annulee'    ? 'selected' : '' }}>❌ Annulée</option>
                    </select>
                </div>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold rounded-full text-sm hover:scale-[1.02] transition-all">Appliquer</button>
            </form>
        </div>
    </div>

    {{-- Sidebar actions --}}
    <div class="space-y-4">
        @if(in_array($reservation->statut, ['en_attente','confirmee']))
        <div class="bg-green-400/5 border border-green-400/20 rounded-2xl p-6">
            <h3 class="text-green-400 font-semibold mb-2">💰 Valider le paiement</h3>
            <p class="text-white/50 text-sm mb-4 leading-relaxed">Le client a payé à la réception. Cliquez pour valider.</p>
            <form method="POST" action="{{ route('gerant.reservations.valider-paiement', $reservation) }}">
                @csrf @method('PATCH')
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-green-500 to-green-400 text-black font-bold rounded-full text-sm hover:scale-[1.02] transition-all">✅ Valider le paiement</button>
            </form>
        </div>
        @endif

        @if($reservation->statut === 'payee')
        <div class="bg-amber-400/5 border border-amber-400/20 rounded-2xl p-6">
            <h3 class="text-amber-400 font-semibold mb-2">📄 Facture PDF</h3>
            <p class="text-white/50 text-sm mb-4">Téléchargez la facture officielle.</p>
            <a href="{{ route('gerant.reservations.facture', $reservation) }}"
               class="flex items-center justify-center gap-2 w-full py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-bold rounded-full text-sm hover:scale-[1.02] transition-all">
                ⬇️ Télécharger la facture
            </a>
        </div>
        @endif

        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 space-y-3">
            <h3 class="text-white/50 text-xs uppercase tracking-widest mb-3">Récapitulatif</h3>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">Nuits</span>
                <span class="text-white font-medium">{{ $reservation->nombreNuits() }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">Prix/nuit</span>
                <span class="text-white">{{ number_format($reservation->chambre?->prix_nuit ?? 0,0,',',' ') }} Ar</span>
            </div>
            <div class="flex justify-between text-sm border-t border-white/10 pt-3">
                <span class="text-white font-semibold">Total</span>
                <span class="text-amber-400 font-bold">{{ number_format($reservation->prix_total,0,',',' ') }} Ar</span>
            </div>
        </div>
    </div>
</div>

@endsection