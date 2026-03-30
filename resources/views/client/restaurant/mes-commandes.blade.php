@extends('layouts.dashboard')
@section('title', 'Mes Commandes')
@include('components.nav-client')
@section('content')

@if(session('success'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success') }}
</div>
@endif

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white">Mes Commandes</h2>
    <p class="text-white/50 text-sm mt-1">Historique de toutes vos commandes au restaurant</p>
</div>

@if($commandes->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">🍽️</p>
    <p class="text-white/60">Aucune commande pour le moment.</p>
    <a href="{{ route('client.restaurant.index') }}"
       class="mt-4 inline-block text-amber-400 hover:text-amber-300 transition text-sm">
        Voir le menu →
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($commandes as $commande)
    <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-white/20 transition">
        <div class="p-5">

            {{-- En-tête commande --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-amber-400 font-mono text-sm font-bold">
                            #{{ str_pad($commande->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <span class="text-white/30">·</span>
                        <p class="text-white/50 text-xs">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <p class="text-white/40 text-xs">{{ $commande->items->count() }} article(s)</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-amber-400 font-bold">{{ number_format($commande->total, 0, ',', ' ') }} Ar</span>
                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold border {{ $commande->couleurStatut() }}">
                        {{ $commande->iconeStatut() }} {{ $commande->libelleStatut() }}
                    </span>
                </div>
            </div>

            {{-- Items résumé --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($commande->items->take(4) as $item)
                <span class="px-3 py-1 bg-white/5 border border-white/10 text-white/60 text-xs rounded-full">
                    {{ $item->menu?->nom ?? '—' }} × {{ $item->quantite }}
                </span>
                @endforeach
                @if($commande->items->count() > 4)
                <span class="px-3 py-1 bg-white/5 border border-white/10 text-white/40 text-xs rounded-full">
                    +{{ $commande->items->count() - 4 }} autres
                </span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-white/5">

                @if($commande->statut === 'en_attente')
                <form method="POST" action="{{ route('client.restaurant.annuler', $commande) }}"
                      onsubmit="return confirm('Annuler cette commande ?')">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="px-4 py-1.5 border border-red-500/20 text-red-400 rounded-full text-xs hover:bg-red-500/10 transition">
                        ❌ Annuler
                    </button>
                </form>
                @endif

                @if(in_array($commande->statut, ['livree', 'prete']))
                <a href="{{ route('client.restaurant.facture', $commande) }}"
                   class="px-4 py-1.5 bg-amber-400/10 border border-amber-400/20 text-amber-400 rounded-full text-xs hover:bg-amber-400/20 transition">
                    📄 Télécharger la facture
                </a>
                @endif

                @if($commande->note)
                <span class="text-white/30 text-xs italic">Note : {{ $commande->note }}</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6">{{ $commandes->links() }}</div>
@endif

@endsection