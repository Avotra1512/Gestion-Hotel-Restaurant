@extends('layouts.dashboard')
@section('title', 'Commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT))
@include('components.nav-gerant')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex items-center gap-4 mb-8">
    <a href="{{ route('gerant.commandes.index') }}"
       class="w-10 h-10 flex items-center justify-center rounded-full border border-white/10
              text-white/60 hover:border-amber-400/50 hover:text-amber-400 transition">←</a>
    <div>
        <h2 class="text-2xl font-bold text-white">
            Commande <span class="text-amber-400">#{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</span>
        </h2>
        <p class="text-white/50 text-sm mt-1">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="ml-auto">
        <span class="px-4 py-2 rounded-full text-sm font-semibold border {{ $commande->couleurStatut() }}">
            {{ $commande->iconeStatut() }} {{ $commande->libelleStatut() }}
        </span>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">

    {{-- COLONNE PRINCIPALE --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Infos client --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4">👤 Client</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Nom</p>
                    <p class="text-white font-medium">{{ $commande->nom }}</p>
                </div>
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Email</p>
                    <p class="text-white">{{ $commande->email }}</p>
                </div>
                @if($commande->user)
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Profil</p>
                    <a href="{{ route('gerant.clients.show', $commande->user) }}"
                       class="text-amber-400 hover:text-amber-300 text-sm transition">Voir le profil →</a>
                </div>
                @endif
                @if($commande->note)
                <div>
                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Note</p>
                    <p class="text-white/70 text-sm">{{ $commande->note }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Détail des plats --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10">
                <h3 class="text-white font-semibold">🍽️ Détail de la commande</h3>
            </div>
            <div class="divide-y divide-white/5">
                @foreach($commande->items as $item)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                        @if($item->menu?->image)
                            <img src="{{ Storage::url($item->menu->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl text-white/20">🍽️</div>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <p class="text-white font-medium">{{ $item->menu?->nom ?? 'Plat supprimé' }}</p>
                        @if($item->menu)
                        <span class="px-2 py-0.5 text-xs rounded-full border {{ $item->menu->couleurCategorie() }}">
                            {{ $item->menu->libelleCategorie() }}
                        </span>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-white/50 text-xs">{{ number_format($item->prix_unitaire, 0, ',', ' ') }} Ar × {{ $item->quantite }}</p>
                        <p class="text-amber-400 font-bold">{{ number_format($item->sous_total, 0, ',', ' ') }} Ar</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-white/10 flex justify-between items-center">
                <span class="text-white font-semibold">Total</span>
                <span class="text-amber-400 font-bold text-2xl">
                    {{ number_format($commande->total, 0, ',', ' ') }}
                    <span class="text-sm font-normal text-white/40">Ar</span>
                </span>
            </div>
        </div>

        {{-- Modifier statut --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-white font-semibold mb-4">🔄 Modifier le statut</h3>
            <form method="POST" action="{{ route('gerant.commandes.statut', $commande) }}"
                  class="flex flex-wrap items-end gap-4">
                @csrf @method('PATCH')
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Nouveau statut</label>
                    <select name="statut"
                            class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-4 py-3 text-sm focus:border-amber-400 focus:outline-none transition">
                        <option value="en_attente"     {{ $commande->statut==='en_attente'     ? 'selected':'' }}>⏳ En attente</option>
                        <option value="en_preparation" {{ $commande->statut==='en_preparation' ? 'selected':'' }}>👨‍🍳 En préparation</option>
                        <option value="prete"          {{ $commande->statut==='prete'          ? 'selected':'' }}>✅ Prête</option>
                        <option value="livree"         {{ $commande->statut==='livree'         ? 'selected':'' }}>🍽️ Livrée</option>
                        <option value="annulee"        {{ $commande->statut==='annulee'        ? 'selected':'' }}>❌ Annulée</option>
                    </select>
                </div>
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-semibold
                               rounded-full text-sm hover:scale-[1.02] transition-all shadow-lg shadow-amber-400/20">
                    Appliquer
                </button>
            </form>
        </div>
    </div>

    {{-- COLONNE ACTIONS --}}
    <div class="space-y-4">

        {{-- Progression du statut --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
            <h3 class="text-white/50 text-xs uppercase tracking-widest mb-5">Progression</h3>
            @php
                $etapes = [
                    'en_attente'     => ['label'=>'En attente',    'emoji'=>'⏳', 'color'=>'text-amber-400'],
                    'en_preparation' => ['label'=>'Préparation',   'emoji'=>'👨‍🍳', 'color'=>'text-blue-400'],
                    'prete'          => ['label'=>'Prête',         'emoji'=>'✅', 'color'=>'text-purple-400'],
                    'livree'         => ['label'=>'Livrée',        'emoji'=>'🍽️', 'color'=>'text-green-400'],
                ];
                $ordre = ['en_attente','en_preparation','prete','livree'];
                $indexActuel = array_search($commande->statut, $ordre);
            @endphp
            <div class="space-y-3">
                @foreach($etapes as $key => $etape)
                @php
                    $index = array_search($key, $ordre);
                    $estPasse = $indexActuel !== false && $index <= $indexActuel && $commande->statut !== 'annulee';
                    $estActuel = $commande->statut === $key;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm flex-shrink-0
                                {{ $estActuel ? 'bg-amber-400 text-black' : ($estPasse ? 'bg-white/10 text-white/60' : 'bg-white/5 text-white/20') }}">
                        {{ $etape['emoji'] }}
                    </div>
                    <span class="text-sm {{ $estActuel ? 'text-white font-semibold' : ($estPasse ? 'text-white/60' : 'text-white/20') }}">
                        {{ $etape['label'] }}
                    </span>
                    @if($estActuel)
                    <span class="ml-auto w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    @endif
                </div>
                @endforeach

                @if($commande->statut === 'annulee')
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-400/10 flex items-center justify-center text-sm">❌</div>
                    <span class="text-red-400 text-sm font-semibold">Annulée</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Actions rapides --}}
        @if($commande->statut === 'en_attente')
        <form method="POST" action="{{ route('gerant.commandes.preparation', $commande) }}">
            @csrf @method('PATCH')
            <button class="w-full py-3 bg-blue-400/10 border border-blue-400/20 text-blue-400 font-semibold
                           rounded-full text-sm hover:bg-blue-400/20 transition">
                👨‍🍳 Passer en préparation
            </button>
        </form>
        @elseif($commande->statut === 'en_preparation')
        <form method="POST" action="{{ route('gerant.commandes.prete', $commande) }}">
            @csrf @method('PATCH')
            <button class="w-full py-3 bg-purple-400/10 border border-purple-400/20 text-purple-400 font-semibold
                           rounded-full text-sm hover:bg-purple-400/20 transition">
                ✅ Marquer comme prête
            </button>
        </form>
        @elseif($commande->statut === 'prete')
        <form method="POST" action="{{ route('gerant.commandes.livree', $commande) }}">
            @csrf @method('PATCH')
            <button class="w-full py-3 bg-green-400/10 border border-green-400/20 text-green-400 font-semibold
                           rounded-full text-sm hover:bg-green-400/20 transition">
                🍽️ Marquer comme livrée
            </button>
        </form>
        @endif

        {{-- Facture PDF --}}
        @if($commande->statut === 'livree')
        <a href="{{ route('gerant.commandes.facture', $commande) }}"
           class="flex items-center justify-center gap-2 w-full py-3
                  bg-gradient-to-r from-amber-400 to-amber-300 text-black font-bold
                  rounded-full text-sm hover:scale-[1.02] transition-all shadow-lg shadow-amber-400/20">
            ⬇️ Télécharger la facture PDF
        </a>
        @endif

        {{-- Récap rapide --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-3">
            <p class="text-white/50 text-xs uppercase tracking-widest">Récapitulatif</p>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">Articles</span>
                <span class="text-white">{{ $commande->items->sum('quantite') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-white/50">Plats distincts</span>
                <span class="text-white">{{ $commande->items->count() }}</span>
            </div>
            <div class="flex justify-between text-sm border-t border-white/10 pt-3">
                <span class="text-white font-semibold">Total</span>
                <span class="text-amber-400 font-bold">{{ number_format($commande->total, 0, ',', ' ') }} Ar</span>
            </div>
        </div>
    </div>
</div>

@endsection