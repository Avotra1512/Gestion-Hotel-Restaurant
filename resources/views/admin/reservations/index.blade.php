@extends('layouts.dashboard')
@section('title', 'Vue Réservations')
@include('components.nav-admin')
@section('content')

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white">Réservations</h2>
        <p class="text-white/50 text-sm mt-1">Vue globale lecture seule — toutes les réservations</p>
    </div>

    {{-- Exports --}}
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.export.reservations', request()->query()) }}"
           class="flex items-center gap-2 px-5 py-2.5 bg-green-400/10 border border-green-400/20
                  text-green-400 rounded-full text-sm font-semibold
                  hover:bg-green-400/20 transition">
            ⬇️ CSV Réservations
        </a>
    </div>
</div>

{{-- STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-gradient-to-br from-amber-400/15 to-transparent border border-amber-400/30 rounded-2xl p-5">
        <p class="text-amber-400/70 text-xs uppercase tracking-widest mb-2">Revenus totaux (payés)</p>
        <p class="text-2xl font-bold text-white">{{ number_format($stats['revenus_total'], 0, ',', ' ') }}</p>
        <p class="text-amber-400/50 text-sm">Ariary</p>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Revenus ce mois</p>
        <p class="text-2xl font-bold text-green-400">{{ number_format($stats['revenus_mois'], 0, ',', ' ') }}</p>
        <p class="text-white/30 text-sm">Ariary</p>
    </div>
    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total nuits vendues</p>
        <p class="text-2xl font-bold text-blue-400">{{ number_format($stats['nuits_total']) }}</p>
        <p class="text-white/30 text-sm">nuits</p>
    </div>
</div>

{{-- ONGLETS STATUT --}}
@php
    $onglets = [
        ''           => ['label'=>'Toutes',     'count'=>$compteurs['tous'],       'color'=>'text-white'],
        'en_attente' => ['label'=>'En attente', 'count'=>$compteurs['en_attente'], 'color'=>'text-amber-400'],
        'confirmee'  => ['label'=>'Confirmées', 'count'=>$compteurs['confirmee'],  'color'=>'text-blue-400'],
        'payee'      => ['label'=>'Payées',     'count'=>$compteurs['payee'],      'color'=>'text-green-400'],
        'terminee'   => ['label'=>'Terminées',  'count'=>$compteurs['terminee'],   'color'=>'text-neutral-400'],
        'annulee'    => ['label'=>'Annulées',   'count'=>$compteurs['annulee'],    'color'=>'text-red-400'],
    ];
    $actuel = request('statut','');
@endphp
<div class="flex flex-wrap gap-2 mb-6">
    @foreach($onglets as $s => $i)
    <a href="{{ route('admin.reservations.index', array_merge(request()->except('page'), ['statut' => $s ?: null])) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-full text-sm transition border
              {{ $actuel === $s ? 'bg-white/10 border-white/20 text-white font-semibold' : 'border-white/10 text-white/50 hover:bg-white/5 hover:text-white' }}">
        {{ $i['label'] }}
        <span class="px-1.5 py-0.5 rounded-full text-xs {{ $i['color'] }} bg-white/5">{{ $i['count'] }}</span>
    </a>
    @endforeach
</div>

{{-- FILTRES --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-5 mb-6">
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="flex flex-wrap gap-4 items-end">
        @if(request('statut')) <input type="hidden" name="statut" value="{{ request('statut') }}"> @endif

        <div class="flex-1 min-w-[180px]">
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Recherche</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom ou email..."
                   class="w-full bg-black/40 border border-white/10 text-white placeholder-white/30
                          rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Type chambre</label>
            <select name="type_chambre"
                    class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm focus:border-amber-400 focus:outline-none transition">
                <option value="">Tous</option>
                <option value="simple" {{ request('type_chambre')==='simple' ? 'selected':'' }}>Simple</option>
                <option value="double" {{ request('type_chambre')==='double' ? 'selected':'' }}>Double</option>
                <option value="triple" {{ request('type_chambre')==='triple' ? 'selected':'' }}>Triple</option>
            </select>
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Du</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                   class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm
                          focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
        </div>
        <div>
            <label class="block text-white/50 text-xs uppercase tracking-widest mb-2">Au</label>
            <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                   class="bg-black/40 border border-white/10 text-white rounded-xl px-4 py-2 text-sm
                          focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
        </div>
        <button type="submit"
                class="px-6 py-2 bg-amber-400 text-black font-semibold rounded-full text-sm hover:bg-amber-300 transition">
            Filtrer
        </button>
        <a href="{{ route('admin.reservations.index') }}"
           class="px-6 py-2 border border-white/10 text-white/60 rounded-full text-sm hover:bg-white/5 transition">
            Réinitialiser
        </a>
    </form>
</div>

{{-- BANNIÈRE INFO LECTURE SEULE --}}
<div class="flex items-center gap-3 p-4 bg-blue-400/5 border border-blue-400/20 rounded-xl mb-6">
    <span class="text-blue-400 text-xl">ℹ️</span>
    <p class="text-blue-400/80 text-sm">
        Mode <strong>lecture seule</strong> — Pour modifier le statut d'une réservation,
        utilisez le tableau de bord du <a href="{{ route('gerant.dashboard') }}" class="underline hover:text-blue-300 transition">gérant</a>.
    </p>
</div>

{{-- TABLEAU --}}
@if($reservations->isEmpty())
<div class="bg-white/5 border border-white/10 rounded-2xl p-16 text-center">
    <p class="text-5xl mb-4">📅</p>
    <p class="text-white/60">Aucune réservation trouvée.</p>
</div>
@else
<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-white/50 text-xs uppercase tracking-widest">
                    <th class="text-left px-5 py-4">Réf.</th>
                    <th class="text-left px-5 py-4">Client</th>
                    <th class="text-left px-5 py-4">Chambre</th>
                    <th class="text-left px-5 py-4">Dates</th>
                    <th class="text-left px-5 py-4">Montant</th>
                    <th class="text-left px-5 py-4">Statut</th>
                    <th class="text-left px-5 py-4">Créé le</th>
                    <th class="text-right px-5 py-4">Détail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @foreach($reservations as $res)
                <tr class="hover:bg-white/5 transition">
                    <td class="px-5 py-4">
                        <span class="text-amber-400/70 font-mono text-xs">
                            #{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-white font-medium">{{ $res->nom }}</p>
                        <p class="text-white/40 text-xs">{{ $res->email }}</p>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-white">{{ $res->chambre?->numero_chambre ?? '—' }}</p>
                        <p class="text-white/40 text-xs capitalize">{{ $res->chambre?->type_chambre }}</p>
                    </td>
                    <td class="px-5 py-4 text-white/70 text-xs">
                        @if($res->date_reservation)
                            {{ $res->date_reservation->format('d/m/Y') }}
                            <span class="text-white/30 block">1 nuit</span>
                        @else
                            {{ $res->date_arrivee?->format('d/m/Y') }}
                            → {{ $res->date_depart?->format('d/m/Y') }}
                            <span class="text-white/30 block">{{ $res->nombreNuits() }} nuit(s)</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-amber-400 font-semibold">
                            {{ number_format($res->prix_total, 0, ',', ' ') }} Ar
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $res->couleurStatut() }}">
                            {{ $res->libelleStatut() }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-white/40 text-xs">
                        {{ $res->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.reservations.show', $res) }}"
                           class="px-4 py-1.5 border border-white/10 text-white/60 rounded-full text-xs
                                  hover:bg-white/5 hover:text-white transition">
                            Voir →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{ $reservations->links() }}
@endif

@endsection