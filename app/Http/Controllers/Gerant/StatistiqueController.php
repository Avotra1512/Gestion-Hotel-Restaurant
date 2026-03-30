<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\Chambre;
use App\Models\User;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    /**
     * Statistiques des ventes (chambres + restaurant).
     */
    public function ventes()
    {
        $aujourd_hui = Carbon::today();
        $debutMois   = Carbon::now()->startOfMonth();
        $debutAnnee  = Carbon::now()->startOfYear();

        // ── Revenus chambres ──────────────────────────────────────
        $revenusChambreJour  = ReservationChambre::where('statut', 'payee')
            ->whereDate('updated_at', $aujourd_hui)->sum('prix_total');
        $revenusChambreMois  = ReservationChambre::where('statut', 'payee')
            ->whereBetween('updated_at', [$debutMois, now()])->sum('prix_total');
        $revenusChambreAnnee = ReservationChambre::where('statut', 'payee')
            ->whereBetween('updated_at', [$debutAnnee, now()])->sum('prix_total');

        // ── Revenus restaurant ────────────────────────────────────
        $revenusRestaurantJour  = CommandeRepas::where('statut', 'livree')
            ->whereDate('updated_at', $aujourd_hui)->sum('total');
        $revenusRestaurantMois  = CommandeRepas::where('statut', 'livree')
            ->whereBetween('updated_at', [$debutMois, now()])->sum('total');
        $revenusRestaurantAnnee = CommandeRepas::where('statut', 'livree')
            ->whereBetween('updated_at', [$debutAnnee, now()])->sum('total');

        // ── Totaux combinés ───────────────────────────────────────
        $stats = [
            // Aujourd'hui
            'revenu_jour_chambre'     => $revenusChambreJour,
            'revenu_jour_restaurant'  => $revenusRestaurantJour,
            'revenu_jour_total'       => $revenusChambreJour + $revenusRestaurantJour,

            // Ce mois
            'revenu_mois_chambre'     => $revenusChambreMois,
            'revenu_mois_restaurant'  => $revenusRestaurantMois,
            'revenu_mois_total'       => $revenusChambreMois + $revenusRestaurantMois,

            // Cette année
            'revenu_annee_chambre'    => $revenusChambreAnnee,
            'revenu_annee_restaurant' => $revenusRestaurantAnnee,
            'revenu_annee_total'      => $revenusChambreAnnee + $revenusRestaurantAnnee,

            // Compteurs
            'reservations_jour'       => ReservationChambre::whereDate('created_at', $aujourd_hui)->count(),
            'commandes_jour'          => CommandeRepas::whereDate('created_at', $aujourd_hui)->count(),
            'reservations_mois'       => ReservationChambre::whereBetween('created_at', [$debutMois, now()])->count(),
            'commandes_mois'          => CommandeRepas::whereBetween('created_at', [$debutMois, now()])->count(),
        ];

        // ── Évolution des 7 derniers jours ────────────────────────
        $evolution = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);
            return [
                'date'        => $date->format('d/m'),
                'jour'        => $date->locale('fr')->dayName,
                'chambres'    => ReservationChambre::where('statut', 'payee')
                    ->whereDate('updated_at', $date)->sum('prix_total'),
                'restaurant'  => CommandeRepas::where('statut', 'livree')
                    ->whereDate('updated_at', $date)->sum('total'),
            ];
        });

        // ── Top 5 plats les plus commandés ce mois ────────────────
        $topPlats = \App\Models\CommandeRepasItem::with('menu')
            ->whereHas('commande', fn($q) => $q
                ->where('statut', 'livree')
                ->whereBetween('updated_at', [$debutMois, now()])
            )
            ->selectRaw('menu_id, SUM(quantite) as total_commande, SUM(sous_total) as total_revenu')
            ->groupBy('menu_id')
            ->orderByDesc('total_commande')
            ->take(5)
            ->get();

        // ── Top 3 chambres les plus réservées ce mois ─────────────
        $topChambres = ReservationChambre::with('chambre')
            ->whereIn('statut', ['payee', 'confirmee', 'terminee'])
            ->whereBetween('created_at', [$debutMois, now()])
            ->selectRaw('chambre_id, COUNT(*) as nb_reservations, SUM(prix_total) as total_revenu')
            ->groupBy('chambre_id')
            ->orderByDesc('nb_reservations')
            ->take(3)
            ->get();

        return view('gerant.statistiques.ventes', compact(
            'stats', 'evolution', 'topPlats', 'topChambres'
        ));
    }

    /**
     * Planning des chambres — occupation calendrier.
     */
    public function planningChambres()
    {
        $chambres = Chambre::with(['reservations' => function ($q) {
            $q->whereIn('statut', ['confirmee', 'payee', 'en_attente'])
              ->where(function ($q2) {
                  $q2->whereDate('date_reservation', '>=', Carbon::today())
                     ->orWhereDate('date_depart', '>=', Carbon::today());
              });
        }])->orderBy('numero_chambre')->get();

        // Générer les 14 prochains jours
        $jours = collect(range(0, 13))->map(fn($i) => Carbon::today()->addDays($i));

        return view('gerant.statistiques.planning', compact('chambres', 'jours'));
    }

    /**
     * Rapport quotidien.
     */
    public function rapportQuotidien()
    {
        $date = request('date')
            ? Carbon::parse(request('date'))
            : Carbon::today();

        // Réservations du jour
        $reservations = ReservationChambre::with(['chambre', 'user'])
            ->whereDate('created_at', $date)
            ->orWhereDate('date_reservation', $date)
            ->orWhereDate('date_arrivee', $date)
            ->get()
            ->unique('id');

        // Commandes du jour
        $commandes = CommandeRepas::with('items.menu')
            ->whereDate('created_at', $date)
            ->get();

        $rapport = [
            'date'                    => $date,
            // Réservations
            'nb_reservations'         => $reservations->count(),
            'reservations_en_attente' => $reservations->where('statut', 'en_attente')->count(),
            'reservations_confirmees' => $reservations->where('statut', 'confirmee')->count(),
            'reservations_payees'     => $reservations->where('statut', 'payee')->count(),
            'revenu_chambres'         => $reservations->where('statut', 'payee')->sum('prix_total'),

            // Commandes
            'nb_commandes'            => $commandes->count(),
            'commandes_en_attente'    => $commandes->where('statut', 'en_attente')->count(),
            'commandes_en_prep'       => $commandes->where('statut', 'en_preparation')->count(),
            'commandes_livrees'       => $commandes->where('statut', 'livree')->count(),
            'revenu_restaurant'       => $commandes->where('statut', 'livree')->sum('total'),

            // Occupation
            'chambres_occupees'       => Chambre::where('statut', 'occupee')->count(),
            'chambres_total'          => Chambre::count(),
        ];

        $rapport['revenu_total'] = $rapport['revenu_chambres'] + $rapport['revenu_restaurant'];

        return view('gerant.statistiques.rapport', compact(
            'rapport', 'reservations', 'commandes', 'date'
        ));
    }
}