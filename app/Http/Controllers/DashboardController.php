<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\Chambre;
use App\Models\Menu;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function client() { return view('client.dashboard'); }
    public function gerant() { return view('gerant.dashboard'); }

    public function admin()
    {
        $aujourd_hui = Carbon::today();
        $debutMois   = Carbon::now()->startOfMonth();
        $debutAnnee  = Carbon::now()->startOfYear();

        // ── Chambres ──────────────────────────────────────────────────
        $stats = [
            // Chambres
            'chambres_total'        => Chambre::count(),
            'chambres_disponibles'  => Chambre::where('statut', 'disponible')->count(),
            'chambres_occupees'     => Chambre::where('statut', 'occupee')->count(),
            'chambres_hors_service' => Chambre::where('statut', 'hors_service')->count(),

            // Réservations
            'reservations_total'    => ReservationChambre::count(),
            'reservations_attente'  => ReservationChambre::where('statut', 'en_attente')->count(),
            'reservations_mois'     => ReservationChambre::whereBetween('created_at', [$debutMois, now()])->count(),

            // Commandes restaurant
            'commandes_total'       => CommandeRepas::count(),
            'commandes_attente'     => CommandeRepas::whereIn('statut', ['en_attente', 'en_preparation'])->count(),
            'commandes_mois'        => CommandeRepas::whereBetween('created_at', [$debutMois, now()])->count(),

            // Menus
            'menus_total'           => Menu::count(),
            'menus_disponibles'     => Menu::where('disponible', true)->count(),

            // Clients
            'clients_total'         => User::where('role', 'client')->count(),
            'clients_mois'          => User::where('role', 'client')
                                        ->whereBetween('created_at', [$debutMois, now()])->count(),

            // Revenus
            'revenus_chambres_total'     => ReservationChambre::where('statut', 'payee')->sum('prix_total'),
            'revenus_chambres_mois'      => ReservationChambre::where('statut', 'payee')
                                                ->whereBetween('updated_at', [$debutMois, now()])->sum('prix_total'),
            'revenus_restaurant_total'   => CommandeRepas::where('statut', 'livree')->sum('total'),
            'revenus_restaurant_mois'    => CommandeRepas::where('statut', 'livree')
                                                ->whereBetween('updated_at', [$debutMois, now()])->sum('total'),
        ];

        $stats['revenus_total']       = $stats['revenus_chambres_total'] + $stats['revenus_restaurant_total'];
        $stats['revenus_mois_total']  = $stats['revenus_chambres_mois']  + $stats['revenus_restaurant_mois'];

        $taux_occupation = $stats['chambres_total'] > 0
            ? round(($stats['chambres_occupees'] / $stats['chambres_total']) * 100)
            : 0;

        // ── Évolution revenus 7 derniers jours ────────────────────────
        $evolution = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);
            return [
                'date'       => $date->format('d/m'),
                'jour'       => $date->locale('fr')->dayName,
                'chambres'   => ReservationChambre::where('statut', 'payee')
                    ->whereDate('updated_at', $date)->sum('prix_total'),
                'restaurant' => CommandeRepas::where('statut', 'livree')
                    ->whereDate('updated_at', $date)->sum('total'),
            ];
        });

        // ── Dernières réservations (5) ────────────────────────────────
        $dernieresReservations = ReservationChambre::with(['chambre', 'user'])
            ->latest()->take(5)->get();

        // ── Dernières commandes (5) ───────────────────────────────────
        $dernieresCommandes = CommandeRepas::with('items.menu')
            ->latest()->take(5)->get();

        // ── Nouveaux clients (5) ──────────────────────────────────────
        $nouveauxClients = User::where('role', 'client')
            ->latest()->take(5)->get();

        // ── Top menus ce mois ─────────────────────────────────────────
        $topMenus = \App\Models\CommandeRepasItem::with('menu')
            ->whereHas('commande', fn($q) => $q
                ->where('statut', 'livree')
                ->whereBetween('updated_at', [$debutMois, now()])
            )
            ->selectRaw('menu_id, SUM(quantite) as total_commande')
            ->groupBy('menu_id')
            ->orderByDesc('total_commande')
            ->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'taux_occupation', 'evolution',
            'dernieresReservations', 'dernieresCommandes',
            'nouveauxClients', 'topMenus'
        ));
    }

}