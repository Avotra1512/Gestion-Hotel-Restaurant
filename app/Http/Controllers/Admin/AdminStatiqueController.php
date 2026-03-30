<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\CommandeRepasItem;
use App\Models\Chambre;
use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminStatiqueController extends Controller
{
    public function index()
    {
        // ── KPI Cards ─────────────────────────────────────────────
        $kpis = [
            'revenus_total'     => ReservationChambre::where('statut','payee')->sum('prix_total')
                                 + CommandeRepas::where('statut','livree')->sum('total'),
            'revenus_mois'      => ReservationChambre::where('statut','payee')
                                       ->whereMonth('updated_at', now()->month)
                                       ->whereYear('updated_at', now()->year)
                                       ->sum('prix_total')
                                 + CommandeRepas::where('statut','livree')
                                       ->whereMonth('updated_at', now()->month)
                                       ->whereYear('updated_at', now()->year)
                                       ->sum('total'),
            'clients_total'     => User::where('role','client')->count(),
            'reservations_total'=> ReservationChambre::count(),
            'commandes_total'   => CommandeRepas::count(),
            'taux_occupation'   => Chambre::count() > 0
                                    ? round((Chambre::where('statut','occupee')->count() / Chambre::count()) * 100)
                                    : 0,
        ];

        // ── 1. Revenus 12 derniers mois (ligne) ───────────────────
        $revenus12Mois = collect(range(11, 0))->map(function ($i) {
            $date = Carbon::now()->subMonths($i);
            $chambres   = ReservationChambre::where('statut','payee')
                ->whereYear('updated_at',  $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('prix_total');
            $restaurant = CommandeRepas::where('statut','livree')
                ->whereYear('updated_at',  $date->year)
                ->whereMonth('updated_at', $date->month)
                ->sum('total');
            return [
                'mois'       => $date->locale('fr')->isoFormat('MMM YYYY'),
                'chambres'   => $chambres,
                'restaurant' => $restaurant,
                'total'      => $chambres + $restaurant,
            ];
        });

        // ── 2. Réservations par statut (donut) ────────────────────
        $reservationsParStatut = [
            'en_attente' => ReservationChambre::where('statut','en_attente')->count(),
            'confirmee'  => ReservationChambre::where('statut','confirmee')->count(),
            'payee'      => ReservationChambre::where('statut','payee')->count(),
            'terminee'   => ReservationChambre::where('statut','terminee')->count(),
            'annulee'    => ReservationChambre::where('statut','annulee')->count(),
        ];

        // ── 3. Commandes par statut (donut) ───────────────────────
        $commandesParStatut = [
            'en_attente'     => CommandeRepas::where('statut','en_attente')->count(),
            'en_preparation' => CommandeRepas::where('statut','en_preparation')->count(),
            'prete'          => CommandeRepas::where('statut','prete')->count(),
            'livree'         => CommandeRepas::where('statut','livree')->count(),
            'annulee'        => CommandeRepas::where('statut','annulee')->count(),
        ];

        // ── 4. Occupation par type de chambre (barres) ────────────
        $occupationParType = collect(['simple','double','triple'])->map(function ($type) {
            $total    = Chambre::where('type_chambre', $type)->count();
            $occupees = Chambre::where('type_chambre', $type)->where('statut','occupee')->count();
            return [
                'type'     => ucfirst($type),
                'total'    => $total,
                'occupees' => $occupees,
                'libres'   => $total - $occupees,
            ];
        });

        // ── 5. Top 10 plats (barres horizontales) ─────────────────
        $topPlats = CommandeRepasItem::with('menu')
            ->selectRaw('menu_id, SUM(quantite) as total_cmd, SUM(sous_total) as total_rev')
            ->groupBy('menu_id')
            ->orderByDesc('total_cmd')
            ->take(10)
            ->get()
            ->filter(fn($i) => $i->menu !== null);

        // ── 6. Nouveaux clients 6 derniers mois (ligne) ───────────
        $clientsMois = collect(range(5, 0))->map(function ($i) {
            $date = Carbon::now()->subMonths($i);
            return [
                'mois'  => $date->locale('fr')->isoFormat('MMM YYYY'),
                'count' => User::where('role','client')
                    ->whereYear('created_at',  $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        });

        // ── 7. Revenus 30 derniers jours (aire) ───────────────────
        $revenus30Jours = collect(range(29, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);
            return [
                'jour'       => $date->format('d/m'),
                'chambres'   => ReservationChambre::where('statut','payee')
                    ->whereDate('updated_at', $date)->sum('prix_total'),
                'restaurant' => CommandeRepas::where('statut','livree')
                    ->whereDate('updated_at', $date)->sum('total'),
            ];
        });

        // ── 8. Réservations par type de chambre (barres) ──────────
        $reservationsParType = collect(['simple','double','triple'])->map(function ($type) {
            return [
                'type'  => ucfirst($type),
                'count' => ReservationChambre::whereHas('chambre', fn($q) =>
                    $q->where('type_chambre', $type)
                )->count(),
            ];
        });

        return view('admin.statistiques.index', compact(
            'kpis',
            'revenus12Mois',
            'reservationsParStatut',
            'commandesParStatut',
            'occupationParType',
            'topPlats',
            'clientsMois',
            'revenus30Jours',
            'reservationsParType'
        ));
    }
}