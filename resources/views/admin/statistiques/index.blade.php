@extends('layouts.dashboard')
@section('title', 'Statistiques')
@include('components.nav-admin')

@section('content')

{{-- EN-TÊTE --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-wide">Statistiques Globales</h2>
        <p class="text-white/50 text-sm mt-1">
            Analyse complète — mis à jour en temps réel
        </p>
    </div>
    <span class="flex items-center gap-2 px-4 py-2 bg-amber-400/10 border border-amber-400/20 rounded-full text-amber-400 text-xs">
        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
        Données en direct
    </span>
</div>

{{-- KPI CARDS ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

    <div class="lg:col-span-1 bg-gradient-to-br from-amber-400/15 to-amber-300/5
                border border-amber-400/30 rounded-2xl p-6">
        <p class="text-amber-400/70 text-xs uppercase tracking-widest mb-2">Revenus totaux</p>
        <p class="text-3xl font-bold text-white">
            {{ number_format($kpis['revenus_total'], 0, ',', ' ') }}
        </p>
        <p class="text-amber-400/60 text-sm mt-1">Ariary</p>
    </div>

    <div class="bg-white/5 border border-green-400/20 rounded-2xl p-6">
        <p class="text-green-400/70 text-xs uppercase tracking-widest mb-2">Revenus ce mois</p>
        <p class="text-3xl font-bold text-green-400">
            {{ number_format($kpis['revenus_mois'], 0, ',', ' ') }}
        </p>
        <p class="text-white/30 text-sm mt-1">Ariary</p>
    </div>

    <div class="bg-white/5 border border-blue-400/20 rounded-2xl p-6">
        <p class="text-blue-400/70 text-xs uppercase tracking-widest mb-2">Taux d'occupation</p>
        <p class="text-3xl font-bold text-blue-400">{{ $kpis['taux_occupation'] }}%</p>
        <div class="w-full bg-white/10 rounded-full h-2 mt-3">
            <div class="h-2 rounded-full bg-gradient-to-r from-blue-400 to-blue-300"
                 style="width: {{ $kpis['taux_occupation'] }}%"></div>
        </div>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Clients inscrits</p>
        <p class="text-3xl font-bold text-white">{{ number_format($kpis['clients_total']) }}</p>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Réservations</p>
        <p class="text-3xl font-bold text-white">{{ number_format($kpis['reservations_total']) }}</p>
    </div>

    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <p class="text-white/50 text-xs uppercase tracking-widest mb-2">Commandes restaurant</p>
        <p class="text-3xl font-bold text-white">{{ number_format($kpis['commandes_total']) }}</p>
    </div>
</div>

{{-- GRAPHIQUE 1 : Revenus 12 mois (ligne) ──────────────────────── --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-white font-semibold">Revenus sur 12 mois</h3>
            <p class="text-white/40 text-xs mt-1">Chambres & Restaurant</p>
        </div>
        <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                <span class="text-white/50">Chambres</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-full bg-blue-400"></div>
                <span class="text-white/50">Restaurant</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                <span class="text-white/50">Total</span>
            </div>
        </div>
    </div>
    <div class="relative h-72">
        <canvas id="chartRevenus12Mois"></canvas>
    </div>
</div>

{{-- GRAPHIQUE 2 : Revenus 30 jours (aire) ──────────────────────── --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="text-white font-semibold">Revenus — 30 derniers jours</h3>
            <p class="text-white/40 text-xs mt-1">Évolution quotidienne</p>
        </div>
    </div>
    <div class="relative h-64">
        <canvas id="chartRevenus30Jours"></canvas>
    </div>
</div>

{{-- LIGNE : Donuts ──────────────────────────────────────────────── --}}
<div class="grid md:grid-cols-2 gap-6 mb-6">

    {{-- GRAPHIQUE 3 : Réservations par statut (donut) --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-1">Réservations par statut</h3>
        <p class="text-white/40 text-xs mb-6">Répartition globale</p>
        <div class="relative h-64 flex items-center justify-center">
            <canvas id="chartReservationsStatut"></canvas>
        </div>
        {{-- Légende --}}
        <div class="grid grid-cols-2 gap-2 mt-4">
            @foreach([
                ['label'=>'En attente',  'val'=>$reservationsParStatut['en_attente'], 'color'=>'bg-amber-400'],
                ['label'=>'Confirmée',   'val'=>$reservationsParStatut['confirmee'],  'color'=>'bg-blue-400'],
                ['label'=>'Payée',       'val'=>$reservationsParStatut['payee'],      'color'=>'bg-green-400'],
                ['label'=>'Terminée',    'val'=>$reservationsParStatut['terminee'],   'color'=>'bg-neutral-400'],
                ['label'=>'Annulée',     'val'=>$reservationsParStatut['annulee'],    'color'=>'bg-red-400'],
            ] as $item)
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full {{ $item['color'] }} flex-shrink-0"></div>
                <span class="text-white/50 text-xs">{{ $item['label'] }}</span>
                <span class="text-white/80 text-xs font-semibold ml-auto">{{ $item['val'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- GRAPHIQUE 4 : Commandes par statut (donut) --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-1">Commandes par statut</h3>
        <p class="text-white/40 text-xs mb-6">Répartition globale</p>
        <div class="relative h-64 flex items-center justify-center">
            <canvas id="chartCommandesStatut"></canvas>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-4">
            @foreach([
                ['label'=>'En attente',    'val'=>$commandesParStatut['en_attente'],     'color'=>'bg-amber-400'],
                ['label'=>'En préparation','val'=>$commandesParStatut['en_preparation'], 'color'=>'bg-blue-400'],
                ['label'=>'Prête',         'val'=>$commandesParStatut['prete'],          'color'=>'bg-purple-400'],
                ['label'=>'Livrée',        'val'=>$commandesParStatut['livree'],         'color'=>'bg-green-400'],
                ['label'=>'Annulée',       'val'=>$commandesParStatut['annulee'],        'color'=>'bg-red-400'],
            ] as $item)
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full {{ $item['color'] }} flex-shrink-0"></div>
                <span class="text-white/50 text-xs">{{ $item['label'] }}</span>
                <span class="text-white/80 text-xs font-semibold ml-auto">{{ $item['val'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- LIGNE : Barres ──────────────────────────────────────────────── --}}
<div class="grid md:grid-cols-2 gap-6 mb-6">

    {{-- GRAPHIQUE 5 : Occupation par type (barres groupées) --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-1">Occupation par type de chambre</h3>
        <p class="text-white/40 text-xs mb-6">Occupées vs Libres</p>
        <div class="relative h-64">
            <canvas id="chartOccupationType"></canvas>
        </div>
    </div>

    {{-- GRAPHIQUE 6 : Réservations par type --}}
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
        <h3 class="text-white font-semibold mb-1">Réservations par type de chambre</h3>
        <p class="text-white/40 text-xs mb-6">Volume total par catégorie</p>
        <div class="relative h-64">
            <canvas id="chartReservationsType"></canvas>
        </div>
    </div>
</div>

{{-- GRAPHIQUE 7 : Top 10 plats (barres horizontales) ─────────────── --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-6">
    <h3 class="text-white font-semibold mb-1">Top 10 plats les plus commandés</h3>
    <p class="text-white/40 text-xs mb-6">Classement par quantité totale commandée</p>
    <div class="relative h-80">
        <canvas id="chartTopPlats"></canvas>
    </div>
</div>

{{-- GRAPHIQUE 8 : Nouveaux clients 6 mois (ligne) ──────────────── --}}
<div class="bg-white/5 border border-white/10 rounded-2xl p-6 mb-6">
    <h3 class="text-white font-semibold mb-1">Nouveaux clients — 6 derniers mois</h3>
    <p class="text-white/40 text-xs mb-6">Évolution mensuelle des inscriptions</p>
    <div class="relative h-56">
        <canvas id="chartNouveauxClients"></canvas>
    </div>
</div>

@endsection

{{-- ════════════════════════════════════════════════════════════════
     SCRIPTS CHART.JS
     ════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Configuration globale Chart.js ────────────────────────────────
Chart.defaults.color          = 'rgba(255,255,255,0.5)';
Chart.defaults.borderColor    = 'rgba(255,255,255,0.07)';
Chart.defaults.font.family    = 'ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size      = 11;
Chart.defaults.plugins.legend.display = false;

// ── Palette de couleurs MISALO ────────────────────────────────────
const AMBER   = '#f59e0b';
const AMBER20 = 'rgba(245,158,11,0.20)';
const AMBER10 = 'rgba(245,158,11,0.08)';
const BLUE    = '#60a5fa';
const BLUE20  = 'rgba(96,165,250,0.20)';
const BLUE10  = 'rgba(96,165,250,0.08)';
const GREEN   = '#4ade80';
const GREEN20 = 'rgba(74,222,128,0.20)';
const PURPLE  = '#c084fc';
const RED     = '#f87171';
const NEUTRAL = '#9ca3af';

// ── Options communes ──────────────────────────────────────────────
const gridOpts = {
    color: 'rgba(255,255,255,0.06)',
    drawBorder: false,
};
const tooltipOpts = {
    backgroundColor: 'rgba(0,0,0,0.85)',
    titleColor: '#f59e0b',
    bodyColor: 'rgba(255,255,255,0.7)',
    borderColor: 'rgba(255,255,255,0.1)',
    borderWidth: 1,
    padding: 12,
    cornerRadius: 8,
    callbacks: {
        label: ctx => ' ' + Number(ctx.parsed.y ?? ctx.parsed).toLocaleString('fr-FR') + ' Ar',
    }
};

// ────────────────────────────────────────────────────────────────
// 1. REVENUS 12 MOIS (ligne)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($revenus12Mois);
    const ctx  = document.getElementById('chartRevenus12Mois').getContext('2d');

    const makeGradient = (ctx, color) => {
        const g = ctx.createLinearGradient(0, 0, 0, 280);
        g.addColorStop(0, color.replace(')', ',0.3)').replace('rgb','rgba'));
        g.addColorStop(1, 'rgba(0,0,0,0)');
        return g;
    };

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.mois),
            datasets: [
                {
                    label: 'Chambres',
                    data: data.map(d => d.chambres),
                    borderColor: AMBER,
                    backgroundColor: AMBER10,
                    borderWidth: 2.5,
                    pointBackgroundColor: AMBER,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Restaurant',
                    data: data.map(d => d.restaurant),
                    borderColor: BLUE,
                    backgroundColor: BLUE10,
                    borderWidth: 2.5,
                    pointBackgroundColor: BLUE,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Total',
                    data: data.map(d => d.total),
                    borderColor: GREEN,
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [6, 3],
                    pointBackgroundColor: GREEN,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    tension: 0.4,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, pointStyleWidth: 8, color: 'rgba(255,255,255,0.5)', font: { size: 11 } }
                },
                tooltip: {
                    ...tooltipOpts,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ' : ' + Number(ctx.parsed.y).toLocaleString('fr-FR') + ' Ar',
                    }
                },
            },
            scales: {
                x: { grid: gridOpts, ticks: { maxRotation: 45 } },
                y: { grid: gridOpts, ticks: { callback: v => Number(v).toLocaleString('fr-FR') + ' Ar' } },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 2. REVENUS 30 JOURS (aire)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($revenus30Jours);
    const ctx  = document.getElementById('chartRevenus30Jours').getContext('2d');

    const gradAmber = ctx.createLinearGradient(0, 0, 0, 256);
    gradAmber.addColorStop(0, 'rgba(245,158,11,0.35)');
    gradAmber.addColorStop(1, 'rgba(245,158,11,0)');

    const gradBlue = ctx.createLinearGradient(0, 0, 0, 256);
    gradBlue.addColorStop(0, 'rgba(96,165,250,0.25)');
    gradBlue.addColorStop(1, 'rgba(96,165,250,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.jour),
            datasets: [
                {
                    label: 'Chambres',
                    data: data.map(d => d.chambres),
                    borderColor: AMBER,
                    backgroundColor: gradAmber,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Restaurant',
                    data: data.map(d => d.restaurant),
                    borderColor: BLUE,
                    backgroundColor: gradBlue,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    tension: 0.4,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, color: 'rgba(255,255,255,0.5)', font: { size: 11 } }
                },
                tooltip: {
                    ...tooltipOpts,
                    callbacks: {
                        label: ctx => ' ' + ctx.dataset.label + ' : ' + Number(ctx.parsed.y).toLocaleString('fr-FR') + ' Ar',
                    }
                },
            },
            scales: {
                x: {
                    grid: gridOpts,
                    ticks: {
                        maxTicksLimit: 10,
                        maxRotation: 0,
                    }
                },
                y: { grid: gridOpts, ticks: { callback: v => Number(v).toLocaleString('fr-FR') + ' Ar' } },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 3. RÉSERVATIONS PAR STATUT (donut)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($reservationsParStatut);
    new Chart(document.getElementById('chartReservationsStatut'), {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'Confirmée', 'Payée', 'Terminée', 'Annulée'],
            datasets: [{
                data: [data.en_attente, data.confirmee, data.payee, data.terminee, data.annulee],
                backgroundColor: [AMBER20, BLUE20, GREEN20, 'rgba(156,163,175,0.2)', 'rgba(248,113,113,0.2)'],
                borderColor:     [AMBER, BLUE, GREEN, NEUTRAL, RED],
                borderWidth: 2,
                hoverOffset: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    titleColor: '#f59e0b',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ' ' + ctx.label + ' : ' + ctx.parsed },
                },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 4. COMMANDES PAR STATUT (donut)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($commandesParStatut);
    new Chart(document.getElementById('chartCommandesStatut'), {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'En préparation', 'Prête', 'Livrée', 'Annulée'],
            datasets: [{
                data: [data.en_attente, data.en_preparation, data.prete, data.livree, data.annulee],
                backgroundColor: [
                    AMBER20, BLUE20,
                    'rgba(192,132,252,0.2)',
                    GREEN20,
                    'rgba(248,113,113,0.2)'
                ],
                borderColor: [AMBER, BLUE, PURPLE, GREEN, RED],
                borderWidth: 2,
                hoverOffset: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    titleColor: '#f59e0b',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ' ' + ctx.label + ' : ' + ctx.parsed },
                },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 5. OCCUPATION PAR TYPE (barres groupées)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($occupationParType);
    new Chart(document.getElementById('chartOccupationType'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.type),
            datasets: [
                {
                    label: 'Occupées',
                    data: data.map(d => d.occupees),
                    backgroundColor: 'rgba(248,113,113,0.6)',
                    borderColor: RED,
                    borderWidth: 1.5,
                    borderRadius: 6,
                },
                {
                    label: 'Libres',
                    data: data.map(d => d.libres),
                    backgroundColor: 'rgba(74,222,128,0.25)',
                    borderColor: GREEN,
                    borderWidth: 1.5,
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { usePointStyle: true, color: 'rgba(255,255,255,0.5)', font: { size: 11 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    titleColor: '#f59e0b',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                },
            },
            scales: {
                x: { grid: gridOpts },
                y: { grid: gridOpts, ticks: { stepSize: 1 } },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 6. RÉSERVATIONS PAR TYPE DE CHAMBRE (barres)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($reservationsParType);
    new Chart(document.getElementById('chartReservationsType'), {
        type: 'bar',
        data: {
            labels: data.map(d => d.type),
            datasets: [{
                label: 'Réservations',
                data: data.map(d => d.count),
                backgroundColor: [AMBER20, BLUE20, 'rgba(192,132,252,0.2)'],
                borderColor:     [AMBER, BLUE, PURPLE],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    titleColor: '#f59e0b',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ' ' + ctx.parsed.y + ' réservation(s)' },
                },
            },
            scales: {
                x: { grid: { display: false } },
                y: { grid: gridOpts, ticks: { stepSize: 1 } },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 7. TOP 10 PLATS (barres horizontales)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($topPlats);
    const noms = data.map(d => d.menu ? d.menu.nom : '—');
    const vals = data.map(d => d.total_cmd);

    // Dégradé de couleurs amber → blue selon le rang
    const colors = data.map((_, i) => {
        const ratio = i / (data.length - 1 || 1);
        const r = Math.round(245 - ratio * (245 - 96));
        const g = Math.round(158 - ratio * (158 - 165));
        const b = Math.round(11  + ratio * (250 - 11));
        return `rgba(${r},${g},${b},0.65)`;
    });
    const borders = data.map((_, i) => {
        const ratio = i / (data.length - 1 || 1);
        const r = Math.round(245 - ratio * (245 - 96));
        const g = Math.round(158 - ratio * (158 - 165));
        const b = Math.round(11  + ratio * (250 - 11));
        return `rgba(${r},${g},${b},1)`;
    });

    new Chart(document.getElementById('chartTopPlats'), {
        type: 'bar',
        data: {
            labels: noms,
            datasets: [{
                label: 'Quantité commandée',
                data: vals,
                backgroundColor: colors,
                borderColor: borders,
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    titleColor: '#f59e0b',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ' ' + ctx.parsed.x + ' commande(s)' },
                },
            },
            scales: {
                x: { grid: gridOpts, ticks: { stepSize: 1 } },
                y: { grid: { display: false }, ticks: { font: { size: 11 } } },
            },
        },
    });
})();

// ────────────────────────────────────────────────────────────────
// 8. NOUVEAUX CLIENTS 6 MOIS (ligne + points)
// ────────────────────────────────────────────────────────────────
(function() {
    const data = @json($clientsMois);
    const ctx  = document.getElementById('chartNouveauxClients').getContext('2d');

    const grad = ctx.createLinearGradient(0, 0, 0, 224);
    grad.addColorStop(0, 'rgba(245,158,11,0.3)');
    grad.addColorStop(1, 'rgba(245,158,11,0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.mois),
            datasets: [{
                label: 'Nouveaux clients',
                data: data.map(d => d.count),
                borderColor: AMBER,
                backgroundColor: grad,
                borderWidth: 2.5,
                pointBackgroundColor: AMBER,
                pointBorderColor: '#000',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
                tension: 0.4,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.85)',
                    titleColor: '#f59e0b',
                    bodyColor: 'rgba(255,255,255,0.7)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: ctx => ' ' + ctx.parsed.y + ' nouveau(x) client(s)' },
                },
            },
            scales: {
                x: { grid: gridOpts },
                y: { grid: gridOpts, ticks: { stepSize: 1 } },
            },
        },
    });
})();
</script>
@endpush