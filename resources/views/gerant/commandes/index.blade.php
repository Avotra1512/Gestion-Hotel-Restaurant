@extends('layouts.dashboard')
@section('title', 'Commandes Restaurant')
@include('components.nav-gerant')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Commandes Restaurant</h2>
        <p class="text-white/50 text-sm mt-1">Gérez les commandes en temps réel</p>
    </div>
    @if($revenuJour > 0)
    <div class="px-5 py-2 bg-green-400/10 border border-green-400/20 rounded-2xl">
        <p class="text-green-400/70 text-xs uppercase tracking-widest">Revenus aujourd'hui</p>
        <p class="text-green-400 font-bold text-lg">{{ number_format($revenuJour, 0, ',', ' ') }} Ar</p>
    </div>
    @endif
</div>

{{-- ONGLETS --}}
@php
    $onglets = [
        ''               => ['label'=>'Toutes',        'count'=>$compteurs['tous'],           'color'=>'text-white'],
        'en_attente'     => ['label'=>'En attente',    'count'=>$compteurs['en_attente'],     'color'=>'text-amber-400'],
        'en_preparation' => ['label'=>'En préparation','count'=>$compteurs['en_preparation'], 'color'=>'text-blue-400'],
        'prete'          => ['label'=>'Prêtes',        'count'=>$compteurs['prete'],          'color'=>'text-purple-400'],
        'livree'         => ['label'=>'Livrées',       'count'=>$compteurs['livree'],         'color'=>'text-green-400'],
        'annulee'        => ['label'=>'Annulées',      'count'=>$compteurs['annulee'],        'color'=>'text-red-400'],
    ];
    $actuel = request('statut','');
@endphp
<div class="flex flex-wrap gap-2 mb-6">
    @foreach($onglets as $s => $i)
    <a href="{{ route('gerant.commandes.index', array_merge(request()->except('page'), ['statut' => $s ?: null])) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-full text-sm transition border
              {{ $actuel === $s ? 'bg-white/10 border-white/20 text-white font-semibold' : 'border-white/10 text-white/50 hover:bg-white/5 hover:text-white' }}">
        {{ $i['label'] }}
        <span class="px-1.5 py-0.5 rounded-full text-xs {{ $i['color'] }} bg-white/5">{{ $i['count'] }}</span>
    </a>
    @endforeach
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('gerant.commandes.index') }}" class="flex flex-wrap gap-4 items-end">
        @if(request('statut')) <input type="hidden" name="statut" value="{{ request('statut') }}"> @endif
        <div class="flex-1 min-w-[200px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche client</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom ou email..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30 rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
        </div>
        <button type="submit" class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">Filtrer</button>
        <a href="{{ route('gerant.commandes.index') }}" class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">Réinitialiser</a>
    </form>
</div>

{{-- LISTE --}}
@if($commandes->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">🍽️</p>
    <p class="text-white/60">Aucune commande trouvée.</p>
</div>
@else
<div class="space-y-3 mb-6">
    @foreach($commandes as $commande)
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition">
        <div class="p-5">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                {{-- Infos commande --}}
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-amber-400/10 border border-amber-400/20
                                flex items-center justify-center text-lg font-bold text-amber-400 flex-shrink-0">
                        {{ strtoupper(substr($commande->nom, 0, 1)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-white font-semibold">{{ $commande->nom }}</p>
                            <span class="text-white/30 text-xs">·</span>
                            <span class="text-amber-400/70 font-mono text-xs">#{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-white/40 text-xs mb-2">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>

                        {{-- Items résumé --}}
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($commande->items->take(3) as $item)
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-white/60 text-xs rounded-full">
                                {{ $item->menu?->nom ?? '—' }} ×{{ $item->quantite }}
                            </span>
                            @endforeach
                            @if($commande->items->count() > 3)
                            <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-white/40 text-xs rounded-full">
                                +{{ $commande->items->count() - 3 }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Montant + Statut + Actions --}}
                <div class="flex flex-wrap items-center gap-3 md:flex-shrink-0">
                    <span class="text-amber-400 font-bold text-lg">
                        {{ number_format($commande->total, 0, ',', ' ') }} Ar
                    </span>

                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $commande->couleurStatut() }}">
                        {{ $commande->iconeStatut() }} {{ $commande->libelleStatut() }}
                    </span>

                    {{-- Actions rapides selon statut --}}
                    @if($commande->statut === 'en_attente')
                    <form method="POST" action="{{ route('gerant.commandes.preparation', $commande) }}">
                        @csrf @method('PATCH')
                        <button class="px-4 py-1.5 bg-blue-400/10 border border-blue-400/20 text-blue-400 rounded-full text-xs hover:bg-blue-400/20 transition">
                            👨‍🍳 Préparer
                        </button>
                    </form>
                    @elseif($commande->statut === 'en_preparation')
                    <form method="POST" action="{{ route('gerant.commandes.prete', $commande) }}">
                        @csrf @method('PATCH')
                        <button class="px-4 py-1.5 bg-purple-400/10 border border-purple-400/20 text-purple-400 rounded-full text-xs hover:bg-purple-400/20 transition">
                            ✅ Prête
                        </button>
                    </form>
                    @elseif($commande->statut === 'prete')
                    <form method="POST" action="{{ route('gerant.commandes.livree', $commande) }}">
                        @csrf @method('PATCH')
                        <button class="px-4 py-1.5 bg-green-400/10 border border-green-400/20 text-green-400 rounded-full text-xs hover:bg-green-400/20 transition">
                            🍽️ Livrer
                        </button>
                    </form>
                    @endif

                    {{-- Voir détail --}}
                    <a href="{{ route('gerant.commandes.show', $commande) }}"
                       class="px-4 py-1.5 border border-white/10 text-white/60 rounded-full text-xs hover:bg-white/5 hover:text-white transition">
                        Voir
                    </a>

                    {{-- Facture PDF si livrée --}}
                    @if($commande->statut === 'livree')
                    <a href="{{ route('gerant.commandes.facture', $commande) }}"
                       class="px-4 py-1.5 bg-amber-400/10 border border-amber-400/20 text-amber-400 rounded-full text-xs hover:bg-amber-400/20 transition">
                        📄 Facture
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{ $commandes->links() }}
@endif

@endsection