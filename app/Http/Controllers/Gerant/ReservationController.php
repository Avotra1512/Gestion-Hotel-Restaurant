<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\ActivityLog;
use App\Notifications\NotifInApp;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = ReservationChambre::with(['chambre', 'user']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type_chambre')) {
            $query->whereHas('chambre', fn($q) => $q->where('type_chambre', $request->type_chambre));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('date_debut')) {
            $query->where(fn($q) => $q->whereDate('date_reservation', '>=', $request->date_debut)
                                      ->orWhereDate('date_arrivee', '>=', $request->date_debut));
        }

        $reservations = $query->latest()->paginate(15)->withQueryString();

        $compteurs = [
            'tous'       => ReservationChambre::count(),
            'en_attente' => ReservationChambre::where('statut', 'en_attente')->count(),
            'confirmee'  => ReservationChambre::where('statut', 'confirmee')->count(),
            'payee'      => ReservationChambre::where('statut', 'payee')->count(),
            'terminee'   => ReservationChambre::where('statut', 'terminee')->count(),
            'annulee'    => ReservationChambre::where('statut', 'annulee')->count(),
        ];

        return view('gerant.reservations.index', compact('reservations', 'compteurs'));
    }

    public function show(ReservationChambre $reservation)
    {
        $reservation->load(['chambre', 'user']);
        return view('gerant.reservations.show', compact('reservation'));
    }

    public function updateStatut(Request $request, ReservationChambre $reservation)
    {
        $request->validate(['statut' => 'required|in:en_attente,confirmee,payee,terminee,annulee']);

        $reservation->update(['statut' => $request->statut]);

        match($request->statut) {
            'confirmee' => NotificationService::clientReservationConfirmee($reservation->load('chambre')),
            'annulee'   => NotificationService::clientReservationRefusee($reservation->load('chambre')),
            default     => null,
        };

        ActivityLog::log(
            'reservation.' . $request->statut,
            Auth::user()->name . ' → Réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT) . ' : ' . $request->statut,
            'reservations',
            match($request->statut) { 'confirmee' => '✅', 'payee' => '💰', 'annulee' => '❌', default => '🔄' },
            match($request->statut) { 'confirmee', 'payee', 'terminee' => 'success', 'annulee' => 'danger', default => 'info' }
        );

        if ($reservation->user && $request->statut === 'confirmee') {
            $reservation->user->notify(new NotifInApp(
                icone:   '✅',
                titre:   'Réservation confirmée',
                message: 'Votre réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT) . ' a été confirmée.',
                lien:    '/client/reservations',
                niveau:  'success',
                module:  'reservations'
            ));
        }

        if ($reservation->user && $request->statut === 'annulee') {
            $reservation->user->notify(new NotifInApp(
                icone:   '❌',
                titre:   'Réservation annulée',
                message: 'Votre réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT) . ' a été annulée.',
                lien:    '/client/reservations',
                niveau:  'danger',
                module:  'reservations'
            ));
        }


        $chambre = $reservation->chambre;
        if ($chambre) {
            if ($request->statut === 'confirmee') {
                $chambre->update(['statut' => 'occupee']);
            } elseif (in_array($request->statut, ['annulee', 'terminee'])) {
                $autresActives = ReservationChambre::where('chambre_id', $chambre->id)
                    ->whereIn('statut', ['confirmee', 'payee'])
                    ->where('id', '!=', $reservation->id)->exists();
                if (!$autresActives) {
                    $chambre->update(['statut' => 'disponible']);
                }
            }
        }

        $message = match($request->statut) {
            'confirmee' => 'Réservation confirmée.',
            'payee'     => 'Paiement validé.',
            'terminee'  => 'Réservation terminée.',
            'annulee'   => 'Réservation annulée.',
            default     => 'Statut mis à jour.',
        };


        return back()->with('success', $message);
    }

    public function validerPaiement(ReservationChambre $reservation)
    {
        if (!in_array($reservation->statut, ['en_attente', 'confirmee'])) {
            return back()->withErrors(['statut' => 'Statut incompatible.']);
        }
        $reservation->update(['statut' => 'payee']);

        NotificationService::clientPaiementValide($reservation->load('chambre'));
        NotificationService::adminsReservationPayee($reservation->load('chambre'));
        ActivityLog::log('reservation.paiement', Auth::user()->name . ' a validé le paiement de la réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT), 'reservations', '💰', 'success');

        if ($reservation->user) {
            $reservation->user->notify(new NotifInApp(
                icone:   '💰',
                titre:   'Paiement validé',
                message: 'Le paiement de votre réservation #' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT) . ' a été validé.',
                lien:    '/client/factures',
                niveau:  'success',
                module:  'reservations'
            ));
        }

        if ($reservation->chambre) {
            $reservation->chambre->update(['statut' => 'occupee']);
        }
        return back()->with('success', '✅ Paiement validé avec succès.');
    }

    public function facturePdf(ReservationChambre $reservation)
    {
        $reservation->load(['chambre', 'user']);
        $pdf = Pdf::loadView('gerant.reservations.facture-pdf', compact('reservation'))
                  ->setPaper('a4', 'portrait');
        $nom = 'facture_MISALO_' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->download($nom);
    }

    public function mettreAJourAuto()
    {
        $today = Carbon::today();
        $reservations = ReservationChambre::whereIn('statut', ['confirmee', 'payee'])
            ->where(fn($q) => $q->whereDate('date_reservation', '<', $today)
                                ->orWhereDate('date_depart', '<', $today))
            ->get();

        foreach ($reservations as $r) {
            /** @var ReservationChambre $r */ 
            $r->update(['statut' => 'terminee']);
            $chambre = $r->chambre;
            if ($chambre && $chambre->statut === 'occupee') {
                $autres = ReservationChambre::where('chambre_id', $chambre->id)
                    ->whereIn('statut', ['confirmee', 'payee'])
                    ->where('id', '!=', $r->id)->exists();
                if (!$autres) $chambre->update(['statut' => 'disponible']);
            }
        }
        return back()->with('success', 'Mise à jour automatique effectuée.');
    }
}
