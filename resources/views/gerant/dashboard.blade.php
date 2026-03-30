@extends('layouts.dashboard')
@section('title', 'Dashboard Gérant')
@include('components.nav-gerant')
@section('content')

@if($alertes > 0)
<div class="mb-6 p-4 bg-amber-400/10 border border-amber-400/30 text-amber-400 rounded-xl text-sm flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="text-xl">⚠️</span>
        <span><strong>{{ $alertes }} réservation(s)</strong> en attente depuis plus de 24h.</span>
    </div>
    <a href="{{ route('gerant.reservations.index', ['statut' => 'en_attente']) }}"
       class="px-4 py-1.5 bg-amber-400 text-black rounded-full text-xs font-bold hover:bg-amber-300 transition">Voir →</a>
</div>
@endif

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-wide">Tableau de bord</h2>
    <p class="text-white/50 text-sm mt-1">Vue d'ensemble — Hôtel MISALO</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('gerant.reservations.index', ['statut'=>'en_attente']) }}"
       class="bg-white/5 border border-amber-400/20 rounded-2xl p-5 hover:border-amber-400/50 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">En attente</p>
        <p class="text-3xl font-bold text-amber-400">{{ $stats['en_attente'] }}</p>
        <p class="text-white/30 text-xs mt-2">réservations</p>
    </a>
    <a href="{{ route('gerant.reservations.index', ['statut'=>'confirmee']) }}"
       class="bg-white/5 border border-blue-400/20 rounded-2xl p-5 hover:border-blue-400/50 transition">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Confirmées</p>
        <p class="text-3xl font-bold text-blue-400">{{ $stats['confirmees'] }}</p>
        <p class="text-white/30 text-xs mt-2">réservations</p>
    </a>
    <div class="bg-white/5 border border-red-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Chambres occupées</p>
        <p class="text-3xl font-bold text-red-400">{{ $stats['chambres_occupees'] }}</p>
        <p class="text-white/30 text-xs mt-2">sur {{ $stats['chambres_total'] }}</p>
    </div>
    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-3">Revenus ce mois</p>
        <p class="text-2xl font-bold text-green-400">{{ number_format($stats['revenus_mois'],0,',',' ') }}</p>
        <p class="text-white/30 text-xs mt-2">Ariary</p>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Total réservations</p>
        <p class="text-3xl font-bold text-white">{{ $stats['reservations_total'] }}</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Revenus totaux</p>
        <p class="text-2xl font-bold text-white">{{ number_format($stats['revenus_total'],0,',',' ') }} <span class="text-sm text-white/40">Ar</span></p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Clients inscrits</p>
        <p class="text-3xl font-bold text-white">{{ $stats['clients_total'] }}</p>
    </div>
</div>

@php $taux = $stats['chambres_total'] > 0 ? round(($stats['chambres_occupees'] / $stats['chambres_total']) * 100) : 0; @endphp
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-8">
    <div class="flex justify-between items-center mb-3">
        <p class="text-white font-semibold">Taux d'occupation</p>
        <p class="text-amber-400 font-bold text-xl">{{ $taux }}%</p>
    </div>
    <div class="w-full bg-white/10 rounded-full h-3">
        <div class="h-3 rounded-full bg-gradient-to-r from-amber-400 to-amber-300" style="width: {{ $taux }}%"></div>
    </div>
    <p class="text-white/40 text-xs mt-2">{{ $stats['chambres_occupees'] }} occupée(s) sur {{ $stats['chambres_total'] }}</p>
</div>

<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-white/10 flex justify-between items-center">
        <h3 class="text-white font-semibold">Dernières réservations</h3>
        <a href="{{ route('gerant.reservations.index') }}" class="text-amber-400 text-sm hover:text-amber-300 transition">Voir tout →</a>
    </div>
    @forelse($dernieresReservations as $res)
    <div class="px-6 py-4 flex items-center justify-between hover:bg-white/5 transition border-b border-white/5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-400/10 border border-amber-400/20 flex items-center justify-center font-bold text-amber-400 text-sm">
                {{ strtoupper(substr($res->nom,0,1)) }}
            </div>
            <div>
                <p class="text-white text-sm font-medium">{{ $res->nom }}</p>
                <p class="text-white/40 text-xs">{{ $res->chambre?->numero_chambre ?? '—' }} · #{{ str_pad($res->id,6,'0',STR_PAD_LEFT) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <p class="text-amber-400 text-sm font-semibold hidden sm:block">{{ number_format($res->prix_total,0,',',' ') }} Ar</p>
            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $res->couleurStatut() }}">{{ $res->libelleStatut() }}</span>
            <a href="{{ route('gerant.reservations.show', $res) }}" class="text-white/40 hover:text-amber-400 text-xs transition">Voir</a>
        </div>
    </div>
    @empty
    <div class="p-10 text-center text-white/40">Aucune réservation.</div>
    @endforelse
</div>

@endsection