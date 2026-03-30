<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    /**
     * Page principale — liste toutes les factures disponibles du client.
     */
    public function index()
    {
        // Réservations payées
        $reservations = ReservationChambre::with('chambre')
            ->where('user_id', Auth::id())
            ->whereIn('statut', ['payee', 'terminee'])
            ->latest()
            ->get();

        // Commandes livrées ou prêtes
        $commandes = CommandeRepas::with('items.menu')
            ->where('user_id', Auth::id())
            ->whereIn('statut', ['livree', 'prete'])
            ->latest()
            ->get();

        $totalReservations = $reservations->sum('prix_total');
        $totalCommandes    = $commandes->sum('total');
        $totalGeneral      = $totalReservations + $totalCommandes;

        return view('client.factures.index', compact(
            'reservations', 'commandes',
            'totalReservations', 'totalCommandes', 'totalGeneral'
        ));
    }

    /**
     * Télécharger la facture PDF d'une réservation chambre.
     */
    public function factureReservation(ReservationChambre $reservation)
    {
        // Sécurité : seul le propriétaire peut télécharger
        abort_if($reservation->user_id !== Auth::id(), 403);

        // Seules les réservations payées ou terminées ont une facture
        abort_if(!in_array($reservation->statut, ['payee', 'terminee']), 403,
            'La facture est disponible uniquement pour les réservations payées.');

        $reservation->load(['chambre', 'user']);

        $pdf = Pdf::loadView('client.factures.reservation-pdf', compact('reservation'))
                  ->setPaper('a4', 'portrait');

        $nom = 'facture_reservation_MISALO_' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nom);
    }

    /**
     * Télécharger la facture PDF d'une commande restaurant.
     */
    public function factureCommande(CommandeRepas $commande)
    {
        abort_if($commande->user_id !== Auth::id(), 403);

        abort_if(!in_array($commande->statut, ['livree', 'prete']), 403,
            'La facture est disponible uniquement pour les commandes livrées.');

        $commande->load(['items.menu', 'user']);

        $pdf = Pdf::loadView('client.factures.commande-pdf', compact('commande'))
                  ->setPaper('a4', 'portrait');

        $nom = 'facture_commande_MISALO_' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nom);
    }

    /**
     * Télécharger une facture groupée (réservation + commandes sur la même période).
     * Optionnel — facture consolidée.
     */
    public function factureGroupee()
    {
        $reservations = ReservationChambre::with('chambre')
            ->where('user_id', Auth::id())
            ->whereIn('statut', ['payee', 'terminee'])
            ->latest()
            ->get();

        $commandes = CommandeRepas::with('items.menu')
            ->where('user_id', Auth::id())
            ->whereIn('statut', ['livree', 'prete'])
            ->latest()
            ->get();

        $totalReservations = $reservations->sum('prix_total');
        $totalCommandes    = $commandes->sum('total');
        $totalGeneral      = $totalReservations + $totalCommandes;

        $client = Auth::user();

        $pdf = Pdf::loadView('client.factures.groupee-pdf', compact(
            'reservations', 'commandes',
            'totalReservations', 'totalCommandes', 'totalGeneral',
            'client'
        ))->setPaper('a4', 'portrait');

        $nom = 'facture_complete_MISALO_' . Auth::id() . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($nom);
    }
}