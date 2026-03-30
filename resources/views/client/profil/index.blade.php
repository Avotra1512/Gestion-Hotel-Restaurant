@extends('layouts.dashboard')
@section('title', 'Mon Profil')
@include('components.nav-client')

@section('content')

{{-- FLASH MESSAGES --}}
@if(session('success_infos'))
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center gap-3">
    <span>✅</span> {{ session('success_infos') }}
</div>
@endif
@if(session('success_password'))
<div class="mb-6 p-4 bg-green-400/10 border border-green-400/30 text-green-400 rounded-xl text-sm flex items-center gap-3">
    <span>🔐</span> {{ session('success_password') }}
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════
     CARTE IDENTITÉ CLIENT
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-gradient-to-br from-white/10 to-transparent border border-white/10
            rounded-3xl p-8 mb-8 relative overflow-hidden">

    {{-- Fond décoratif --}}
    <div class="absolute -top-20 -right-20 w-64 h-64 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 relative z-10">

        {{-- Avatar --}}
        <div class="relative">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400/30 to-amber-300/10
                        border-2 border-amber-400/40 flex items-center justify-center
                        text-3xl font-bold text-amber-400 shadow-lg shadow-amber-400/10">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-green-400
                        border-2 border-black flex items-center justify-center">
                <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
            </div>
        </div>

        {{-- Infos principales --}}
        <div class="flex-grow">
            <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
            <p class="text-white/50 text-sm mt-1">{{ $user->email }}</p>
            <div class="flex flex-wrap items-center gap-3 mt-3">
                <span class="px-3 py-1 bg-blue-400/10 border border-blue-400/20 text-blue-400 rounded-full text-xs font-semibold">
                    👤 Client
                </span>
                <span class="text-white/30 text-xs">
                    Membre depuis {{ $user->created_at->locale('fr')->isoFormat('MMMM YYYY') }}
                </span>
                @if($user->active)
                <span class="flex items-center gap-1.5 text-green-400 text-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
                    Compte actif
                </span>
                @endif
            </div>
        </div>

        {{-- Stats rapides --}}
        <div class="flex gap-6 text-center">
            <div>
                <p class="text-2xl font-bold text-amber-400">{{ $statsReservations['total'] }}</p>
                <p class="text-white/40 text-xs mt-0.5">Réservations</p>
            </div>
            <div class="w-px bg-white/10"></div>
            <div>
                <p class="text-2xl font-bold text-blue-400">{{ $statsCommandes['total'] }}</p>
                <p class="text-white/40 text-xs mt-0.5">Commandes</p>
            </div>
            <div class="w-px bg-white/10"></div>
            <div>
                <p class="text-lg font-bold text-green-400">{{ number_format($totalDepense, 0, ',', ' ') }}</p>
                <p class="text-white/40 text-xs mt-0.5">Ar dépensés</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLETS DE NAVIGATION
     ═══════════════════════════════════════════════════════════ --}}
@php $tab = session('tab', request('tab', 'apercu')); @endphp

<div class="flex flex-wrap gap-2 mb-8 border-b border-white/10 pb-4">
    @foreach([
        ['key'=>'apercu',       'label'=>'Aperçu',          'emoji'=>'📊'],
        ['key'=>'reservations', 'label'=>'Réservations',     'emoji'=>'📅'],
        ['key'=>'commandes',    'label'=>'Commandes',        'emoji'=>'🍽️'],
        ['key'=>'infos',        'label'=>'Mes informations', 'emoji'=>'✏️'],
        ['key'=>'securite',     'label'=>'Sécurité',         'emoji'=>'🔐'],
    ] as $t)
    <button onclick="switchTab('{{ $t['key'] }}')"
            id="tab-btn-{{ $t['key'] }}"
            class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm transition-all duration-200
                   {{ $tab === $t['key']
                        ? 'bg-amber-400 text-black font-bold shadow-lg shadow-amber-400/20'
                        : 'text-white/60 hover:bg-white/5 hover:text-white border border-white/10' }}">
        <span>{{ $t['emoji'] }}</span>
        <span>{{ $t['label'] }}</span>
    </button>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLET : APERÇU
     ═══════════════════════════════════════════════════════════ --}}
<div id="tab-apercu" class="{{ $tab !== 'apercu' ? 'hidden' : '' }}">

    {{-- Stats détaillées --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white/5 border border-amber-400/20 rounded-2xl p-5">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Réservations</p>
            <p class="text-3xl font-bold text-amber-400">{{ $statsReservations['total'] }}</p>
            <div class="mt-3 space-y-1">
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">En attente</span>
                    <span class="text-amber-400">{{ $statsReservations['en_attente'] }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">Confirmées</span>
                    <span class="text-blue-400">{{ $statsReservations['confirmee'] }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">Payées</span>
                    <span class="text-green-400">{{ $statsReservations['payee'] }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">Terminées</span>
                    <span class="text-neutral-400">{{ $statsReservations['terminee'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-5">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Commandes</p>
            <p class="text-3xl font-bold text-blue-400">{{ $statsCommandes['total'] }}</p>
            <div class="mt-3 space-y-1">
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">Livrées</span>
                    <span class="text-green-400">{{ $statsCommandes['livrees'] }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">Annulées</span>
                    <span class="text-red-400">{{ $statsCommandes['annulees'] }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total dépensé</p>
            <p class="text-2xl font-bold text-green-400">
                {{ number_format($totalDepense, 0, ',', ' ') }}
            </p>
            <p class="text-white/30 text-xs mt-1">Ariary</p>
            <div class="mt-3 space-y-1">
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">🛏️ Chambres</span>
                    <span class="text-white/70">{{ number_format($statsReservations['depenses'], 0, ',', ' ') }} Ar</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-white/40">🍽️ Restaurant</span>
                    <span class="text-white/70">{{ number_format($statsCommandes['depenses'], 0, ',', ' ') }} Ar</span>
                </div>
            </div>
        </div>

        <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Chambre favorite</p>
            @if($chambreFavorite)
                <p class="text-xl font-bold text-white">{{ $chambreFavorite['chambre']?->numero_chambre ?? '—' }}</p>
                <p class="text-white/40 text-xs capitalize mt-1">
                    {{ $chambreFavorite['chambre']?->type_chambre }}
                </p>
                <p class="text-amber-400/70 text-xs mt-1">
                    Réservée {{ $chambreFavorite['count'] }} fois
                </p>
            @else
                <p class="text-white/30 text-sm mt-2">Aucune</p>
            @endif
        </div>
    </div>

    {{-- Dernières activités --}}
    <div class="grid md:grid-cols-2 gap-6">

        {{-- Dernière réservation --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-semibold text-sm">🛏️ Dernière réservation</h3>
                <button onclick="switchTab('reservations')"
                        class="text-amber-400/70 text-xs hover:text-amber-400 transition">
                    Voir toutes →
                </button>
            </div>
            @if($derniereReservation)
            <div class="p-5">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-white font-semibold">{{ $derniereReservation->chambre?->numero_chambre ?? '—' }}</p>
                        <p class="text-white/40 text-xs">
                            #{{ str_pad($derniereReservation->id, 6, '0', STR_PAD_LEFT) }}
                            · {{ $derniereReservation->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $derniereReservation->couleurStatut() }}">
                        {{ $derniereReservation->libelleStatut() }}
                    </span>
                </div>
                <p class="text-amber-400 font-bold">
                    {{ number_format($derniereReservation->prix_total, 0, ',', ' ') }} Ar
                </p>
                @if($derniereReservation->date_reservation)
                    <p class="text-white/40 text-xs mt-1">{{ $derniereReservation->date_reservation->format('d/m/Y') }} · 1 nuit</p>
                @elseif($derniereReservation->date_arrivee)
                    <p class="text-white/40 text-xs mt-1">
                        {{ $derniereReservation->date_arrivee->format('d/m/Y') }}
                        → {{ $derniereReservation->date_depart?->format('d/m/Y') }}
                    </p>
                @endif
            </div>
            @else
            <div class="p-8 text-center text-white/30 text-sm">Aucune réservation.</div>
            @endif
        </div>

        {{-- Dernière commande --}}
        <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10 flex justify-between items-center">
                <h3 class="text-white font-semibold text-sm">🍽️ Dernière commande</h3>
                <button onclick="switchTab('commandes')"
                        class="text-amber-400/70 text-xs hover:text-amber-400 transition">
                    Voir toutes →
                </button>
            </div>
            @if($derniereCommande)
            <div class="p-5">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="text-white font-semibold">
                            #{{ str_pad($derniereCommande->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <p class="text-white/40 text-xs">{{ $derniereCommande->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $derniereCommande->couleurStatut() }}">
                        {{ $derniereCommande->libelleStatut() }}
                    </span>
                </div>
                <p class="text-amber-400 font-bold mb-2">
                    {{ number_format($derniereCommande->total, 0, ',', ' ') }} Ar
                </p>
                <div class="flex flex-wrap gap-1">
                    @foreach($derniereCommande->items->take(3) as $item)
                    <span class="px-2 py-0.5 bg-white/5 border border-white/10 text-white/50 text-xs rounded-full">
                        {{ $item->menu?->nom ?? '—' }} ×{{ $item->quantite }}
                    </span>
                    @endforeach
                </div>
            </div>
            @else
            <div class="p-8 text-center text-white/30 text-sm">Aucune commande.</div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLET : RÉSERVATIONS
     ═══════════════════════════════════════════════════════════ --}}
<div id="tab-reservations" class="{{ $tab !== 'reservations' ? 'hidden' : '' }}">

    <div class="flex justify-between items-center mb-5">
        <h3 class="text-white font-semibold">Toutes mes réservations</h3>
        <a href="{{ route('client.chambres.index') }}"
           class="px-5 py-2 bg-amber-400/10 border border-amber-400/20 text-amber-400 rounded-full text-sm hover:bg-amber-400/20 transition">
            + Nouvelle réservation
        </a>
    </div>

    @if($reservations->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-12 text-center">
        <p class="text-4xl mb-3">🛏️</p>
        <p class="text-white/50">Aucune réservation pour le moment.</p>
        <a href="{{ route('client.chambres.index') }}"
           class="mt-3 inline-block text-amber-400 hover:text-amber-300 text-sm transition">
            Voir nos chambres →
        </a>
    </div>
    @else
    <div class="space-y-3">
        @foreach($reservations as $res)
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5
                    hover:border-white/20 transition flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex items-center gap-4 flex-grow">
                <div class="w-12 h-12 rounded-xl overflow-hidden bg-neutral-900 flex-shrink-0">
                    @if($res->chambre?->image)
                        <img src="{{ Storage::url($res->chambre->image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-xl text-white/20">🛏️</div>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-white font-semibold">{{ $res->chambre?->numero_chambre ?? '—' }}</p>
                        <span class="text-white/30 text-xs capitalize">{{ $res->chambre?->type_chambre }}</span>
                        <span class="text-amber-400/50 font-mono text-xs">#{{ str_pad($res->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <p class="text-white/40 text-xs mt-0.5">
                        @if($res->date_reservation)
                            {{ $res->date_reservation->format('d/m/Y') }} · 1 nuit
                        @else
                            {{ $res->date_arrivee?->format('d/m/Y') }}
                            → {{ $res->date_depart?->format('d/m/Y') }}
                            · {{ $res->nombreNuits() }} nuits
                        @endif
                    </p>
                    <p class="text-white/30 text-xs">
                        Créée le {{ $res->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="text-amber-400 font-bold">{{ number_format($res->prix_total, 0, ',', ' ') }} Ar</span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $res->couleurStatut() }}">
                    {{ $res->libelleStatut() }}
                </span>
                {{-- Annuler si en attente --}}
                @if($res->statut === 'en_attente')
                <form method="POST" action="{{ route('client.reservations.annuler', $res) }}"
                      onsubmit="return confirm('Annuler cette réservation ?')">
                    @csrf @method('PATCH')
                    <button class="px-3 py-1 border border-red-500/20 text-red-400 rounded-full text-xs hover:bg-red-500/10 transition">
                        Annuler
                    </button>
                </form>
                @endif
                {{-- Facture si payée --}}
                @if(in_array($res->statut, ['payee','terminee']))
                <a href="{{ route('client.factures.reservation', $res) }}"
                   class="px-3 py-1 border border-amber-400/20 text-amber-400 rounded-full text-xs hover:bg-amber-400/10 transition">
                    📄 PDF
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLET : COMMANDES
     ═══════════════════════════════════════════════════════════ --}}
<div id="tab-commandes" class="{{ $tab !== 'commandes' ? 'hidden' : '' }}">

    <div class="flex justify-between items-center mb-5">
        <h3 class="text-white font-semibold">Toutes mes commandes</h3>
        <a href="{{ route('client.restaurant.index') }}"
           class="px-5 py-2 bg-amber-400/10 border border-amber-400/20 text-amber-400 rounded-full text-sm hover:bg-amber-400/20 transition">
            + Nouvelle commande
        </a>
    </div>

    @if($commandes->isEmpty())
    <div class="bg-white/5 border border-white/10 rounded-2xl p-12 text-center">
        <p class="text-4xl mb-3">🍽️</p>
        <p class="text-white/50">Aucune commande pour le moment.</p>
        <a href="{{ route('client.restaurant.index') }}"
           class="mt-3 inline-block text-amber-400 hover:text-amber-300 text-sm transition">
            Voir le menu →
        </a>
    </div>
    @else
    <div class="space-y-3">
        @foreach($commandes as $cmd)
        <div class="bg-white/5 border border-white/10 rounded-2xl p-5
                    hover:border-white/20 transition">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-amber-400 font-mono font-bold text-sm">
                            #{{ str_pad($cmd->id, 6, '0', STR_PAD_LEFT) }}
                        </p>
                        <span class="text-white/30 text-xs">·</span>
                        <p class="text-white/50 text-xs">{{ $cmd->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <p class="text-white/30 text-xs mt-0.5">{{ $cmd->items->count() }} plat(s)</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-amber-400 font-bold">{{ number_format($cmd->total, 0, ',', ' ') }} Ar</span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $cmd->couleurStatut() }}">
                        {{ $cmd->iconeStatut() }} {{ $cmd->libelleStatut() }}
                    </span>
                    @if($cmd->statut === 'en_attente')
                    <form method="POST" action="{{ route('client.restaurant.annuler', $cmd) }}"
                          onsubmit="return confirm('Annuler cette commande ?')">
                        @csrf @method('PATCH')
                        <button class="px-3 py-1 border border-red-500/20 text-red-400 rounded-full text-xs hover:bg-red-500/10 transition">
                            Annuler
                        </button>
                    </form>
                    @endif
                    @if(in_array($cmd->statut, ['livree','prete']))
                    <a href="{{ route('client.factures.commande', $cmd) }}"
                       class="px-3 py-1 border border-amber-400/20 text-amber-400 rounded-full text-xs hover:bg-amber-400/10 transition">
                        📄 PDF
                    </a>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach($cmd->items as $item)
                <span class="px-2.5 py-1 bg-white/5 border border-white/10 text-white/50 text-xs rounded-full">
                    {{ $item->menu?->nom ?? '—' }} ×{{ $item->quantite }}
                </span>
                @endforeach
            </div>
            @if($cmd->note)
            <p class="text-white/30 text-xs mt-2 italic">Note : {{ $cmd->note }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLET : MES INFORMATIONS
     ═══════════════════════════════════════════════════════════ --}}
<div id="tab-infos" class="{{ $tab !== 'infos' ? 'hidden' : '' }}">

    <div class="max-w-xl">
        <h3 class="text-white font-semibold mb-6">Modifier mes informations</h3>

        <form method="POST" action="{{ route('client.profil.infos') }}"
              class="bg-white/5 border border-white/10 rounded-2xl p-7 space-y-5">
            @csrf @method('PATCH')

            {{-- Nom --}}
            <div>
                <label class="block text-white/60 text-sm tracking-wide mb-2">
                    Nom complet <span class="text-amber-400">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                              focus:border-amber-400 focus:outline-none transition
                              {{ $errors->has('name') ? 'border-red-500' : 'border-white/10' }}" />
                @error('name') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-white/60 text-sm tracking-wide mb-2">
                    Adresse email <span class="text-amber-400">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full bg-black/40 border text-white rounded-xl px-4 py-3 text-sm
                              focus:border-amber-400 focus:outline-none transition
                              {{ $errors->has('email') ? 'border-red-500' : 'border-white/10' }}" />
                @error('email') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Infos non modifiables --}}
            <div class="p-4 bg-white/5 rounded-xl border border-white/10 space-y-2">
                <p class="text-white/40 text-xs uppercase tracking-widest mb-2">Informations du compte</p>
                <div class="flex justify-between text-sm">
                    <span class="text-white/50">Rôle</span>
                    <span class="text-white/70">Client</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-white/50">Membre depuis</span>
                    <span class="text-white/70">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-white/50">Statut du compte</span>
                    <span class="text-green-400 text-xs">● Actif</span>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300
                               text-black font-semibold rounded-full text-sm
                               shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     ONGLET : SÉCURITÉ
     ═══════════════════════════════════════════════════════════ --}}
<div id="tab-securite" class="{{ $tab !== 'securite' ? 'hidden' : '' }}">

    <div class="max-w-xl space-y-6">

        {{-- Changer le mot de passe --}}
        <div>
            <h3 class="text-white font-semibold mb-5">🔐 Changer le mot de passe</h3>

            <form method="POST" action="{{ route('client.profil.password') }}"
                  class="bg-white/5 border border-white/10 rounded-2xl p-7 space-y-5">
                @csrf @method('PATCH')

                {{-- Mot de passe actuel --}}
                <div>
                    <label class="block text-white/60 text-sm tracking-wide mb-2">
                        Mot de passe actuel <span class="text-amber-400">*</span>
                    </label>
                    <input type="password" name="current_password"
                           placeholder="Votre mot de passe actuel"
                           class="w-full bg-black/40 border text-white placeholder-white/20 rounded-xl px-4 py-3 text-sm
                                  focus:border-amber-400 focus:outline-none transition
                                  {{ $errors->has('current_password') ? 'border-red-500' : 'border-white/10' }}" />
                    @error('current_password')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div>
                    <label class="block text-white/60 text-sm tracking-wide mb-2">
                        Nouveau mot de passe <span class="text-amber-400">*</span>
                    </label>
                    <input type="password" name="password"
                           placeholder="Min. 8 caractères"
                           class="w-full bg-black/40 border text-white placeholder-white/20 rounded-xl px-4 py-3 text-sm
                                  focus:border-amber-400 focus:outline-none transition
                                  {{ $errors->has('password') ? 'border-red-500' : 'border-white/10' }}" />
                    @error('password')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div>
                    <label class="block text-white/60 text-sm tracking-wide mb-2">
                        Confirmer le nouveau mot de passe <span class="text-amber-400">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="Répéter le nouveau mot de passe"
                           class="w-full bg-black/40 border border-white/10 text-white placeholder-white/20 rounded-xl px-4 py-3 text-sm
                                  focus:border-amber-400 focus:outline-none transition" />
                </div>

                {{-- Règles --}}
                <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-white/40 text-xs mb-2">Le nouveau mot de passe doit :</p>
                    <ul class="space-y-1 text-xs text-white/30">
                        <li>• Contenir au moins 8 caractères</li>
                        <li>• Être différent de l'ancien</li>
                    </ul>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-amber-400 to-amber-300
                                   text-black font-semibold rounded-full text-sm
                                   shadow-lg shadow-amber-400/20 hover:scale-[1.02] transition-all">
                        Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>

        {{-- Zone danger --}}
        <div>
            <h3 class="text-red-400 font-semibold mb-4">⚠️ Zone de danger</h3>

            <div class="bg-red-500/5 border border-red-500/20 rounded-2xl p-7">
                <h4 class="text-white font-medium mb-2">Supprimer mon compte</h4>
                <p class="text-white/40 text-sm mb-5 leading-relaxed">
                    Cette action est <strong class="text-white/70">irréversible</strong>.
                    Toutes vos données seront définitivement supprimées :
                    réservations, commandes et historique.
                </p>

                {{-- Formulaire suppression --}}
                <form method="POST" action="{{ route('client.profil.delete') }}"
                      id="form-delete-account">
                    @csrf @method('DELETE')

                    <div class="mb-4">
                        <label class="block text-white/50 text-sm mb-2">
                            Tapez <span class="text-red-400 font-bold font-mono">SUPPRIMER</span> pour confirmer
                        </label>
                        <input type="text" name="confirm_delete"
                               placeholder="SUPPRIMER"
                               class="w-full bg-black/40 border text-white placeholder-white/20 rounded-xl px-4 py-3 text-sm
                                      focus:border-red-400 focus:outline-none transition
                                      {{ $errors->has('confirm_delete') ? 'border-red-500' : 'border-red-500/20' }}" />
                        @error('confirm_delete')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            onclick="return confirm('Êtes-vous absolument sûr ? Cette action est irréversible.')"
                            class="px-6 py-2.5 bg-red-500/10 border border-red-500/30 text-red-400
                                   rounded-full text-sm font-semibold
                                   hover:bg-red-500 hover:text-white transition-all">
                        Supprimer définitivement mon compte
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ════════════════════════════════════════════════════════════════
     SCRIPT ONGLETS
     ════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
function switchTab(name) {
    // Masquer tous les onglets
    ['apercu','reservations','commandes','infos','securite'].forEach(t => {
        document.getElementById('tab-' + t)?.classList.add('hidden');
        const btn = document.getElementById('tab-btn-' + t);
        if (btn) {
            btn.classList.remove('bg-amber-400','text-black','font-bold','shadow-lg','shadow-amber-400/20');
            btn.classList.add('text-white/60','hover:bg-white/5','hover:text-white','border','border-white/10');
        }
    });

    // Afficher l'onglet actif
    document.getElementById('tab-' + name)?.classList.remove('hidden');
    const activeBtn = document.getElementById('tab-btn-' + name);
    if (activeBtn) {
        activeBtn.classList.add('bg-amber-400','text-black','font-bold','shadow-lg','shadow-amber-400/20');
        activeBtn.classList.remove('text-white/60','hover:bg-white/5','hover:text-white','border','border-white/10');
    }
}
</script>
@endpush
