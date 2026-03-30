@extends('layouts.dashboard')
@section('title', 'Programme Fidélité')
@section('sidebar-links') @include('components.nav-client') @endsection
@section('content')

{{-- ═══════════════════════════════════════════════════════════
     CARTE NIVEAU ACTUEL
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-gradient-to-br {{ $niveau['gradient'] }} border {{ $niveau['bordure'] }}
            rounded-3xl p-8 mb-8 relative overflow-hidden">

    {{-- Fond décoratif --}}
    <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full blur-3xl opacity-20
                {{ str_replace('text-', 'bg-', $niveau['couleur']) }} pointer-events-none"></div>
    <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full blur-2xl opacity-10
                {{ str_replace('text-', 'bg-', $niveau['couleur']) }} pointer-events-none"></div>

    <div class="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-6">

        {{-- Badge niveau --}}
        <div class="flex-shrink-0">
            <div class="w-24 h-24 rounded-full {{ $niveau['bg'] }} border-2 {{ $niveau['bordure'] }}
                        flex items-center justify-center shadow-lg">
                <span class="text-5xl">{{ $niveau['icone'] }}</span>
            </div>
        </div>

        {{-- Infos niveau --}}
        <div class="flex-grow">
            <p class="text-white/50 text-xs uppercase tracking-widest mb-1">Votre niveau actuel</p>
            <h2 class="text-4xl font-black {{ $niveau['couleur'] }} mb-2">
                {{ $niveau['nom'] }}
            </h2>
            <p class="text-white/60 text-sm">{{ $niveau['desc'] }}</p>

            @if($niveau['remise'] > 0)
            <div class="mt-3 inline-flex items-center gap-2 px-4 py-1.5 {{ $niveau['bg'] }}
                        border {{ $niveau['bordure'] }} rounded-full">
                <span class="{{ $niveau['couleur'] }} font-bold text-sm">{{ $niveau['remise'] }}% de remise</span>
                <span class="text-white/40 text-xs">sur vos réservations</span>
            </div>
            @endif
        </div>

        {{-- Points total --}}
        <div class="text-right flex-shrink-0">
            <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Points accumulés</p>
            <p class="text-5xl font-black {{ $niveau['couleur'] }}">
                {{ number_format($pointsTotal) }}
            </p>
            <p class="text-white/40 text-xs mt-1">points</p>
        </div>
    </div>

    {{-- Barre de progression vers le niveau suivant --}}
    @if(!$progression['max_atteint'])
    <div class="relative z-10 mt-8 pt-6 border-t border-white/10">
        <div class="flex justify-between items-center mb-2">
            <span class="text-white/50 text-xs">
                Niveau actuel : <span class="{{ $niveau['couleur'] }} font-semibold">{{ $niveau['nom'] }}</span>
            </span>
            <span class="text-white/50 text-xs">
                Prochain : <span class="text-white font-semibold">
                    {{ $progression['prochain']['icone'] }} {{ $progression['prochain']['nom'] }}
                </span>
            </span>
        </div>
        <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-1000
                        bg-gradient-to-r {{ str_replace('text-', 'from-', $niveau['couleur']) }} to-white/50"
                 style="width: {{ $progression['pourcentage'] }}%">
            </div>
        </div>
        <div class="flex justify-between mt-2">
            <span class="{{ $niveau['couleur'] }} text-xs font-semibold">
                {{ number_format($pointsTotal) }} pts
            </span>
            <span class="text-white/40 text-xs">
                encore <strong class="text-white">{{ number_format($progression['restant']) }} points</strong>
                pour atteindre {{ $progression['prochain']['nom'] }}
            </span>
            <span class="text-white/40 text-xs">
                {{ number_format($progression['points_requis']) }} pts
            </span>
        </div>
    </div>
    @else
    <div class="relative z-10 mt-6 pt-6 border-t border-white/10 text-center">
        <p class="text-cyan-400 font-bold">🎉 Félicitations ! Vous avez atteint le niveau maximum — Diamant !</p>
        <p class="text-white/50 text-sm mt-1">Vous bénéficiez de tous les avantages VIP MISALO.</p>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════
     STATISTIQUES
     ═══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
        <p class="text-3xl font-bold text-amber-400">{{ $stats['nb_nuits'] }}</p>
        <p class="text-white/50 text-xs mt-1 uppercase tracking-widest">Nuits séjournées</p>
        <p class="text-white/30 text-xs mt-1">+{{ $pointsChambres }} pts</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
        <p class="text-3xl font-bold text-blue-400">{{ $stats['nb_commandes'] }}</p>
        <p class="text-white/50 text-xs mt-1 uppercase tracking-widest">Commandes passées</p>
        <p class="text-white/30 text-xs mt-1">+{{ $pointsRestaurant }} pts</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
        <p class="text-3xl font-bold text-white">{{ $stats['nb_sejours'] }}</p>
        <p class="text-white/50 text-xs mt-1 uppercase tracking-widest">Séjours effectués</p>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-center">
        <p class="text-xl font-bold text-green-400">{{ number_format($stats['total_depense'], 0, ',', ' ') }}</p>
        <p class="text-white/50 text-xs mt-1 uppercase tracking-widest">Ariary dépensés</p>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     TOUS LES NIVEAUX
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-8">
    <h3 class="text-white font-semibold mb-6">Tous les niveaux</h3>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['nom'=>'Bronze',  'icone'=>'🥉', 'min'=>0,    'max'=>499,  'remise'=>0,  'couleur'=>'text-orange-400',  'bordure'=>'border-orange-400/20',  'bg'=>'bg-orange-400/5'],
            ['nom'=>'Argent',  'icone'=>'🥈', 'min'=>500,  'max'=>1499, 'remise'=>5,  'couleur'=>'text-neutral-300', 'bordure'=>'border-neutral-300/20', 'bg'=>'bg-neutral-300/5'],
            ['nom'=>'Or',      'icone'=>'🥇', 'min'=>1500, 'max'=>2999, 'remise'=>10, 'couleur'=>'text-amber-400',   'bordure'=>'border-amber-400/20',   'bg'=>'bg-amber-400/5'],
            ['nom'=>'Diamant', 'icone'=>'💎', 'min'=>3000, 'max'=>null, 'remise'=>15, 'couleur'=>'text-cyan-400',    'bordure'=>'border-cyan-400/20',    'bg'=>'bg-cyan-400/5'],
        ] as $niv)
        @php $estActuel = $niveau['nom'] === $niv['nom']; @endphp
        <div class="rounded-2xl p-5 border transition
                    {{ $estActuel ? $niv['bg'] . ' ' . $niv['bordure'] . ' ring-1 ' . str_replace('border-', 'ring-', $niv['bordure']) : 'bg-white/3 border-white/5' }}">
            <div class="text-center mb-4">
                <span class="text-4xl">{{ $niv['icone'] }}</span>
                <p class="{{ $estActuel ? $niv['couleur'] : 'text-white/50' }} font-bold mt-2">
                    {{ $niv['nom'] }}
                    @if($estActuel)
                        <span class="text-xs font-normal opacity-70"> ← vous</span>
                    @endif
                </p>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-white/40">Points requis</span>
                    <span class="{{ $estActuel ? $niv['couleur'] : 'text-white/60' }} font-medium">
                        {{ number_format($niv['min']) }}{{ $niv['max'] ? ' – ' . number_format($niv['max']) : '+' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-white/40">Remise</span>
                    <span class="{{ $niv['remise'] > 0 ? ($estActuel ? $niv['couleur'] : 'text-green-400/70') : 'text-white/30' }} font-medium">
                        {{ $niv['remise'] > 0 ? $niv['remise'] . '%' : '—' }}
                    </span>
                </div>
                @if($niv['nom'] === 'Or')
                <div class="pt-1 border-t border-white/5">
                    <span class="text-white/30">Priorité confirmation</span>
                </div>
                @endif
                @if($niv['nom'] === 'Diamant')
                <div class="pt-1 border-t border-white/5">
                    <span class="text-white/30">Service VIP</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     COMMENT GAGNER DES POINTS
     ═══════════════════════════════════════════════════════════ --}}
<div class="grid md:grid-cols-2 gap-4 mb-8">
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-4">Comment gagner des points ?</h3>
        <div class="space-y-3">
            <div class="flex items-center gap-4 p-3 bg-amber-400/5 border border-amber-400/10 rounded-xl">
                <span class="text-2xl">🛏️</span>
                <div class="flex-grow">
                    <p class="text-white text-sm font-medium">Réservation payée</p>
                    <p class="text-white/40 text-xs">Par nuit séjournée</p>
                </div>
                <span class="text-amber-400 font-bold text-lg">+10 pts</span>
            </div>
            <div class="flex items-center gap-4 p-3 bg-blue-400/5 border border-blue-400/10 rounded-xl">
                <span class="text-2xl">🍽️</span>
                <div class="flex-grow">
                    <p class="text-white text-sm font-medium">Commande livrée</p>
                    <p class="text-white/40 text-xs">Par commande au restaurant</p>
                </div>
                <span class="text-blue-400 font-bold text-lg">+5 pts</span>
            </div>
        </div>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-4">Vos points en détail</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-amber-400/5 border border-amber-400/10 rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🛏️</span>
                    <span class="text-white/70 text-sm">Points chambres</span>
                </div>
                <span class="text-amber-400 font-bold">{{ number_format($pointsChambres) }} pts</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-blue-400/5 border border-blue-400/10 rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🍽️</span>
                    <span class="text-white/70 text-sm">Points restaurant</span>
                </div>
                <span class="text-blue-400 font-bold">{{ number_format($pointsRestaurant) }} pts</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-white/5 border border-white/10 rounded-xl border-t-2 border-t-white/20">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⭐</span>
                    <span class="text-white font-semibold">Total</span>
                </div>
                <span class="{{ $niveau['couleur'] }} font-black text-xl">{{ number_format($pointsTotal) }} pts</span>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════
     HISTORIQUE DES POINTS
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-white/10">
        <h3 class="text-white font-semibold">Historique des gains</h3>
        <p class="text-white/40 text-xs mt-0.5">Toutes vos transactions de points</p>
    </div>

    @if($historiquePoints->isEmpty())
    <div class="p-12 text-center">
        <p class="text-4xl mb-3">⭐</p>
        <p class="text-white/50 text-sm">Aucun point encore gagné.</p>
        <a href="{{ route('client.chambres.index') }}"
           class="mt-3 inline-block text-amber-400 hover:text-amber-300 transition text-sm">
            Réserver une chambre →
        </a>
    </div>
    @else
    <div class="divide-y divide-white/5">
        @foreach($historiquePoints as $h)
        <div class="px-6 py-4 flex items-center gap-4 hover:bg-white/5 transition">
            <div class="w-10 h-10 rounded-full bg-white/5 border border-white/10
                        flex items-center justify-center text-xl flex-shrink-0">
                {{ $h['icone'] }}
            </div>
            <div class="flex-grow">
                <p class="text-white text-sm">{{ $h['description'] }}</p>
                <p class="text-white/30 text-xs mt-0.5">
                    {{ \Carbon\Carbon::parse($h['date'])->format('d/m/Y à H:i') }}
                </p>
            </div>
            <span class="{{ $h['couleur'] }} font-bold text-lg">
                {{ $h['points'] }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

@endsection