<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReservationViewController extends Controller
{
    /**
     * Vue globale lecture seule — toutes les réservations.
     */
    public function index(Request $request)
    {
        $query = ReservationChambre::with(['chambre', 'user']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type_chambre')) {
            $query->whereHas('chambre', fn($q) =>
                $q->where('type_chambre', $request->type_chambre)
            );
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('nom', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
            );
        }
        if ($request->filled('date_debut')) {
            $query->where(fn($q) =>
                $q->whereDate('date_reservation', '>=', $request->date_debut)
                  ->orWhereDate('date_arrivee', '>=', $request->date_debut)
            );
        }
        if ($request->filled('date_fin')) {
            $query->where(fn($q) =>
                $q->whereDate('date_reservation', '<=', $request->date_fin)
                  ->orWhereDate('date_depart', '<=', $request->date_fin)
            );
        }

        $reservations = $query->latest()->paginate(20)->withQueryString();

        $compteurs = [
            'tous'       => ReservationChambre::count(),
            'en_attente' => ReservationChambre::where('statut', 'en_attente')->count(),
            'confirmee'  => ReservationChambre::where('statut', 'confirmee')->count(),
            'payee'      => ReservationChambre::where('statut', 'payee')->count(),
            'terminee'   => ReservationChambre::where('statut', 'terminee')->count(),
            'annulee'    => ReservationChambre::where('statut', 'annulee')->count(),
        ];

        $stats = [
            'revenus_total' => ReservationChambre::where('statut', 'payee')->sum('prix_total'),
            'revenus_mois'  => ReservationChambre::where('statut', 'payee')
                                   ->whereMonth('updated_at', now()->month)
                                   ->sum('prix_total'),
            'nuits_total'   => ReservationChambre::whereIn('statut', ['payee', 'terminee'])
                                   ->get()
                                   ->sum(fn($r) => $r->nombreNuits()),
        ];

        return view('admin.reservations.index', compact(
            'reservations', 'compteurs', 'stats'
        ));
    }

    /**
     * Détail lecture seule d'une réservation.
     */
    public function show(ReservationChambre $reservation)
    {
        $reservation->load(['chambre', 'user']);
        return view('admin.reservations.show', compact('reservation'));
    }

    // ══════════════════════════════════════════════════════════════
    // EXPORTS CSV
    // ══════════════════════════════════════════════════════════════

    /**
     * Export CSV — toutes les réservations.
     */
    public function exportReservationsCsv(Request $request)
    {
        $query = ReservationChambre::with(['chambre', 'user']);

        // Appliquer les mêmes filtres
        if ($request->filled('statut'))      $query->where('statut', $request->statut);
        if ($request->filled('date_debut'))  $query->whereDate('created_at', '>=', $request->date_debut);
        if ($request->filled('date_fin'))    $query->whereDate('created_at', '<=', $request->date_fin);

        $reservations = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reservations_misalo_' . now()->format('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Référence',
                'Client',
                'Email',
                'Chambre',
                'Type chambre',
                'Date réservation',
                'Date arrivée',
                'Date départ',
                'Nuits',
                'Prix total (Ar)',
                'Statut',
                'Motif',
                'Créé le',
            ], ';');

            foreach ($reservations as $r) {
                fputcsv($file, [
                    $r->id,
                    '#' . str_pad($r->id, 6, '0', STR_PAD_LEFT),
                    $r->nom,
                    $r->email,
                    $r->chambre?->numero_chambre ?? '—',
                    $r->chambre?->type_chambre ?? '—',
                    $r->date_reservation?->format('d/m/Y') ?? '—',
                    $r->date_arrivee?->format('d/m/Y') ?? '—',
                    $r->date_depart?->format('d/m/Y') ?? '—',
                    $r->nombreNuits(),
                    $r->prix_total,
                    $r->libelleStatut(),
                    $r->motif ?? '—',
                    $r->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export CSV — tous les clients.
     */
    public function exportClientsCsv(Request $request)
    {
        $query = User::where('role', 'client')
            ->withCount('reservationChambres');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
            );
        }
        if ($request->filled('active')) {
            $query->where('active', $request->active === '1');
        }

        $clients = $query->latest()->get()->map(function ($c) {
            $depenses = ReservationChambre::where('user_id', $c->id)
                ->whereIn('statut', ['payee', 'terminee'])
                ->sum('prix_total')
                + CommandeRepas::where('user_id', $c->id)
                ->where('statut', 'livree')
                ->sum('total');
            $c->total_depenses = $depenses;
            return $c;
        });

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="clients_misalo_' . now()->format('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($clients) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'ID',
                'Nom',
                'Email',
                'Statut compte',
                'Nb réservations',
                'Total dépensé (Ar)',
                'Inscrit le',
                'Dernière connexion',
            ], ';');

            foreach ($clients as $c) {
                fputcsv($file, [
                    $c->id,
                    $c->name,
                    $c->email,
                    $c->active ? 'Actif' : 'Inactif',
                    $c->reservation_chambres_count,
                    $c->total_depenses,
                    $c->created_at->format('d/m/Y'),
                    $c->updated_at->format('d/m/Y'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export CSV — commandes restaurant.
     */
    public function exportCommandesCsv(Request $request)
    {
        $query = CommandeRepas::with(['user', 'items.menu']);

        if ($request->filled('statut'))     $query->where('statut', $request->statut);
        if ($request->filled('date_debut')) $query->whereDate('created_at', '>=', $request->date_debut);
        if ($request->filled('date_fin'))   $query->whereDate('created_at', '<=', $request->date_fin);

        $commandes = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="commandes_misalo_' . now()->format('Ymd_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($commandes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'ID',
                'Référence',
                'Client',
                'Email',
                'Nb plats',
                'Total (Ar)',
                'Statut',
                'Note',
                'Date commande',
            ], ';');

            foreach ($commandes as $c) {
                fputcsv($file, [
                    $c->id,
                    '#' . str_pad($c->id, 6, '0', STR_PAD_LEFT),
                    $c->nom,
                    $c->email,
                    $c->items->sum('quantite'),
                    $c->total,
                    $c->libelleStatut(),
                    $c->note ?? '—',
                    $c->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}