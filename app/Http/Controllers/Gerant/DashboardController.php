<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\Chambre;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'reservations_total' => ReservationChambre::count(),
            'en_attente'         => ReservationChambre::where('statut', 'en_attente')->count(),
            'confirmees'         => ReservationChambre::where('statut', 'confirmee')->count(),
            'payees'             => ReservationChambre::where('statut', 'payee')->count(),
            'chambres_occupees'  => Chambre::where('statut', 'occupee')->count(),
            'chambres_total'     => Chambre::count(),
            'revenus_total'      => ReservationChambre::where('statut', 'payee')->sum('prix_total'),
            'revenus_mois'       => ReservationChambre::where('statut', 'payee')
                                        ->whereMonth('updated_at', Carbon::now()->month)
                                        ->sum('prix_total'),
            'clients_total'      => User::where('role', 'client')->count(),
        ];

        $dernieresReservations = ReservationChambre::with(['chambre', 'user'])
            ->latest()->take(5)->get();

        $alertes = ReservationChambre::where('statut', 'en_attente')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->count();

        return view('gerant.dashboard', compact('stats', 'dernieresReservations', 'alertes'));
    }
}