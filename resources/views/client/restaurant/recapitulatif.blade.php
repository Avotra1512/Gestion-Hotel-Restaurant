@extends('layouts.dashboard')
@section('title', 'Mon Panier')
@include('components.nav-client')
@section('content')

<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('client.restaurant.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div>
        <h2 class="text-2xl font-bold text-white">Mon Panier</h2>
        <p class="text-white/50 text-sm mt-1">Vérifiez votre commande avant de confirmer</p>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-8 max-w-5xl">

    {{-- LISTE DES ARTICLES --}}
    <div class="lg:col-span-2 space-y-4">

        @foreach($panier as $key => $item)
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 flex items-center gap-4">

            {{-- Image mini --}}
            <div class="w-16 h-16 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                @if($item['image'])
                    <img src="{{ Storage::url($item['image']) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-2xl text-white/20">🍽️</div>
                @endif
            </div>

            {{-- Infos --}}
            <div class="flex-grow">
                <p class="text-white font-semibold">{{ $item['nom'] }}</p>
                <p class="text-amber-400 text-sm">{{ number_format($item['prix_unitaire'], 0, ',', ' ') }} Ar / unité</p>
            </div>

            {{-- Quantité modifiable --}}
            <form method="POST" action="{{ route('client.restaurant.panier.modifier') }}" class="flex items-center gap-2">
                @csrf @method('PATCH')
                <input type="hidden" name="key" value="{{ $key }}">
                <input type="number" name="quantite" value="{{ $item['quantite'] }}" min="1" max="20"
                       onchange="this.form.submit()"
                       class="w-16 bg-black/40 border border-white/10 text-white text-center
                              rounded-xl px-2 py-1.5 text-sm focus:border-amber-400 focus:outline-none transition">
            </form>

            {{-- Sous-total --}}
            <div class="text-right min-w-[90px]">
                <p class="text-white font-bold">{{ number_format($item['sous_total'], 0, ',', ' ') }} Ar</p>
            </div>

            {{-- Supprimer --}}
            <form method="POST" action="{{ route('client.restaurant.panier.supprimer') }}">
                @csrf @method('DELETE')
                <input type="hidden" name="key" value="{{ $key }}">
                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full
                                             border border-red-500/20 text-red-400 hover:bg-red-500/10 transition text-xs">
                    ✕
                </button>
            </form>
        </div>
        @endforeach

        {{-- Vider le panier --}}
        <form method="POST" action="{{ route('client.restaurant.panier.vider') }}" class="text-right">
            @csrf @method('DELETE')
            <button type="submit" class="text-white/30 hover:text-red-400 text-xs transition">
                🗑 Vider le panier
            </button>
        </form>
    </div>

    {{-- RÉSUMÉ + CONFIRMATION --}}
    <div class="lg:col-span-1">
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6 sticky top-6">

            <h3 class="text-white font-semibold mb-5">Résumé de la commande</h3>

            <div class="space-y-3 mb-5">
                @foreach($panier as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-white/60">{{ $item['nom'] }} × {{ $item['quantite'] }}</span>
                    <span class="text-white">{{ number_format($item['sous_total'], 0, ',', ' ') }} Ar</span>
                </div>
                @endforeach
            </div>

            <div class="border-t border-white/10 pt-4 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-white font-semibold">Total</span>
                    <span class="text-amber-400 font-bold text-2xl">
                        {{ number_format($totalPanier, 0, ',', ' ') }}
                        <span class="text-sm font-normal text-white/40">Ar</span>
                    </span>
                </div>
            </div>

            {{-- Formulaire confirmation --}}
            <form method="POST" action="{{ route('client.restaurant.confirmer') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-white/60 text-xs uppercase tracking-widest mb-2">
                        Note (optionnel)
                    </label>
                    <textarea name="note" rows="3"
                              placeholder="Allergie, demande spéciale..."
                              class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                                     rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none
                                     transition resize-none"></textarea>
                </div>

                <div class="p-3 bg-amber-400/5 border border-amber-400/20 rounded-xl">
                    <p class="text-amber-400/80 text-xs leading-relaxed">
                        💡 Le paiement s'effectue à la caisse du restaurant après service.
                    </p>
                </div>

                <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-amber-400 to-amber-300
                               text-black font-bold rounded-full text-sm
                               shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all">
                    ✅ Confirmer la commande
                </button>
            </form>
        </div>
    </div>
</div>

@endsection