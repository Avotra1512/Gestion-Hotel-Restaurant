@extends('layouts.dashboard')
@section('title', 'Exports CSV')
@include('components.nav-admin')
@section('content')

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white">Centre d'export</h2>
    <p class="text-white/50 text-sm mt-1">Exportez vos données en CSV compatible Excel</p>
</div>

<div class="grid md:grid-cols-3 gap-6">

    {{-- Export Réservations --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6
                hover:border-green-400/30 transition">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-green-400/10 border border-green-400/20
                        flex items-center justify-center text-2xl">📅</div>
            <div>
                <h3 class="text-white font-semibold">Réservations</h3>
                <p class="text-white/40 text-xs">Toutes les réservations</p>
            </div>
        </div>
        <p class="text-white/50 text-sm mb-5 leading-relaxed">
            Exporte toutes les réservations avec client, chambre, dates, montant et statut.
        </p>

        {{-- Filtres optionnels --}}
        <form method="GET" action="{{ route('admin.export.reservations') }}" class="space-y-3">
            <div>
                <label class="block text-white/40 text-xs mb-1">Statut (optionnel)</label>
                <select name="statut" class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="confirmee">Confirmée</option>
                    <option value="payee">Payée</option>
                    <option value="terminee">Terminée</option>
                    <option value="annulee">Annulée</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-white/40 text-xs mb-1">Du</label>
                    <input type="date" name="date_debut"
                           class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-white/40 text-xs mb-1">Au</label>
                    <input type="date" name="date_fin"
                           class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
                </div>
            </div>
            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-green-500 to-green-400 text-black font-bold
                           rounded-full text-sm hover:scale-[1.02] transition-all shadow-lg shadow-green-400/20">
                ⬇️ Télécharger CSV
            </button>
        </form>
    </div>

    {{-- Export Clients --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6
                hover:border-blue-400/30 transition">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-400/10 border border-blue-400/20
                        flex items-center justify-center text-2xl">👥</div>
            <div>
                <h3 class="text-white font-semibold">Clients</h3>
                <p class="text-white/40 text-xs">Liste des clients inscrits</p>
            </div>
        </div>
        <p class="text-white/50 text-sm mb-5 leading-relaxed">
            Exporte tous les clients avec nom, email, nombre de réservations et total dépensé.
        </p>
        <form method="GET" action="{{ route('admin.export.clients') }}" class="space-y-3">
            <div>
                <label class="block text-white/40 text-xs mb-1">Statut compte</label>
                <select name="active" class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition">
                    <option value="">Tous</option>
                    <option value="1">Actifs uniquement</option>
                    <option value="0">Inactifs uniquement</option>
                </select>
            </div>
            <div>
                <label class="block text-white/40 text-xs mb-1">Recherche (optionnel)</label>
                <input type="text" name="search" placeholder="Nom ou email..."
                       class="w-full bg-black/40 border border-white/10 text-white placeholder-white/20 rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition">
            </div>
            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-400 text-black font-bold
                           rounded-full text-sm hover:scale-[1.02] transition-all shadow-lg shadow-blue-400/20">
                ⬇️ Télécharger CSV
            </button>
        </form>
    </div>

    {{-- Export Commandes --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6
                hover:border-amber-400/30 transition">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-amber-400/10 border border-amber-400/20
                        flex items-center justify-center text-2xl">🍽️</div>
            <div>
                <h3 class="text-white font-semibold">Commandes Restaurant</h3>
                <p class="text-white/40 text-xs">Toutes les commandes</p>
            </div>
        </div>
        <p class="text-white/50 text-sm mb-5 leading-relaxed">
            Exporte toutes les commandes restaurant avec client, plats, montant et statut.
        </p>
        <form method="GET" action="{{ route('admin.export.commandes') }}" class="space-y-3">
            <div>
                <label class="block text-white/40 text-xs mb-1">Statut (optionnel)</label>
                <select name="statut" class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente">En attente</option>
                    <option value="en_preparation">En préparation</option>
                    <option value="prete">Prête</option>
                    <option value="livree">Livrée</option>
                    <option value="annulee">Annulée</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-white/40 text-xs mb-1">Du</label>
                    <input type="date" name="date_debut"
                           class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-white/40 text-xs mb-1">Au</label>
                    <input type="date" name="date_fin"
                           class="w-full bg-black/40 border border-white/10 text-white rounded-xl px-3 py-2 text-xs focus:border-amber-400 focus:outline-none transition [color-scheme:dark]">
                </div>
            </div>
            <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-amber-400 to-amber-300 text-black font-bold
                           rounded-full text-sm hover:scale-[1.02] transition-all shadow-lg shadow-amber-400/20">
                ⬇️ Télécharger CSV
            </button>
        </form>
    </div>
</div>

{{-- Infos format --}}
<div class="mt-8 p-5 bg-white/5 border border-white/10 rounded-2xl flex items-start gap-3">
    <span class="text-amber-400 text-xl">💡</span>
    <div>
        <p class="text-white font-medium text-sm">Format CSV compatible Excel</p>
        <p class="text-white/40 text-xs mt-1 leading-relaxed">
            Les fichiers sont encodés en UTF-8 avec BOM pour une compatibilité optimale avec Microsoft Excel et LibreOffice Calc.
            Séparateur : point-virgule (;). Les dates sont au format JJ/MM/AAAA.
        </p>
    </div>
</div>

@endsection