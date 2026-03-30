@extends('layouts.dashboard')
@section('title', 'Commande confirmée')
@include('components.nav-client')
@section('content')

<div class="max-w-2xl mx-auto">

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full
                    bg-gradient-to-br from-amber-400/20 to-amber-300/10
                    border border-amber-400/30 mb-6">
            <span class="text-4xl">🍽️</span>
        </div>
        <h2 class="text-3xl font-bold text-white mb-2">Commande enregistrée !</h2>
        <p class="text-white/50">Votre commande a bien été prise en compte.</p>
    </div>

    {{-- CARTE COMMANDE --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">

        <div class="bg-gradient-to-r from-amber-400/10 to-transparent border-b border-white/10 px-6 py-4 flex justify-between items-center">
            <div>
                <p class="text-white/50 text-xs uppercase tracking-widest">Numéro de commande</p>
                <p class="text-amber-400 font-bold text-lg">#{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</p>
            </div>
            <span class="px-4 py-1.5 rounded-full text-xs font-semibold border {{ $commande->couleurStatut() }}">
                {{ $commande->iconeStatut() }} {{ $commande->libelleStatut() }}
            </span>
        </div>

        {{-- Items --}}
        <div class="p-6">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-4">Détail de la commande</p>
            <div class="space-y-3">
                @foreach($commande->items as $item)
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-white/30">×{{ $item->quantite }}</span>
                        <span class="text-white">{{ $item->menu?->nom ?? 'Plat supprimé' }}</span>
                    </div>
                    <span class="text-amber-400/80">{{ number_format($item->sous_total, 0, ',', ' ') }} Ar</span>
                </div>
                @endforeach
            </div>

            @if($commande->note)
            <div class="mt-4 p-3 bg-white/5 rounded-xl">
                <p class="text-white/40 text-xs mb-1">Note</p>
                <p class="text-white/70 text-sm">{{ $commande->note }}</p>
            </div>
            @endif

            <div class="border-t border-white/10 mt-5 pt-4 flex justify-between items-center">
                <span class="text-white font-semibold">Total</span>
                <span class="text-amber-400 font-bold text-xl">
                    {{ number_format($commande->total, 0, ',', ' ') }}
                    <span class="text-sm font-normal text-white/40">Ar</span>
                </span>
            </div>
        </div>
    </div>

    {{-- NOTE PAIEMENT --}}
    <div class="p-5 bg-amber-400/5 border border-amber-400/20 rounded-2xl mb-8 flex items-start gap-3">
        <span class="text-amber-400 text-2xl">💳</span>
        <div>
            <p class="text-amber-400 font-semibold text-sm">Paiement à la caisse</p>
            <p class="text-white/60 text-sm mt-1 leading-relaxed">
                Le règlement de <strong class="text-white">{{ number_format($commande->total, 0, ',', ' ') }} Ar</strong>
                s'effectue directement à la caisse du restaurant MISALO.
                Présentez votre numéro <strong class="text-amber-400">#{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</strong>.
            </p>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="{{ route('client.restaurant.commandes') }}"
           class="flex items-center justify-center gap-2 px-8 py-3
                  bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                  rounded-full shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all text-sm">
            🧾 Voir mes commandes
        </a>
        <a href="{{ route('client.restaurant.index') }}"
           class="flex items-center justify-center gap-2 px-8 py-3
                  border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            🍽️ Retour au menu
        </a>
    </div>
</div>

@endsection