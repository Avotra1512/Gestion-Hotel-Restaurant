<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Chambre;
use App\Models\ReservationChambre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;
use App\Notifications\NotifInApp;
use App\Models\User;
use App\Services\NotificationService;

class ReservationChambreController extends Controller
{
    /**
     * Liste des chambres visibles par le client.
     * Toutes les chambres sont affichées, mais seules les disponibles
     * ont le bouton "Réserver" actif.
     */
    public function index(Request $request)
    {
        $query = Chambre::query();

        if ($request->filled('type_chambre')) {
            $query->where('type_chambre', $request->type_chambre);
        }

        // On exclut uniquement hors_service
        // Plus de notion "occupée" — toutes les chambres sont réservables
        $query->where('statut', '!=', 'hors_service');

        $chambres = $query->orderBy('prix_nuit')->get();

        return view('client.chambres.index', compact('chambres'));
    }

    /**
     * Formulaire de réservation d'une chambre.
     */
    public function create(Chambre $chambre)
    {
        // UNIQUEMENT les réservations confirmées bloquent les dates
        // en_attente = la chambre reste libre pour d'autres
        $reservations = ReservationChambre::where('chambre_id', $chambre->id)
            ->where('statut', 'confirmee')  // ← seulement confirmée
            ->get();

        $datesIndisponibles = [];

        foreach ($reservations as $reservation) {
            if ($reservation->date_reservation) {
                $datesIndisponibles[] = $reservation->date_reservation->format('Y-m-d');
            } elseif ($reservation->date_arrivee && $reservation->date_depart) {
                $current = $reservation->date_arrivee->copy();
                $end     = $reservation->date_depart->copy();
                while ($current < $end) {
                    $datesIndisponibles[] = $current->format('Y-m-d');
                    $current->addDay();
                }
            }
        }

        $datesIndisponibles = array_unique(array_values($datesIndisponibles));

        return view('client.chambres.reserver', compact('chambre', 'datesIndisponibles'));
    }

    /**
     * Enregistre la réservation.
     * Pas de paiement en ligne — statut = en_attente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'chambre_id'       => 'required|exists:chambres,id',
            'mode_reservation' => 'required|in:nuit_unique,sejour',
            'date_reservation' => 'required_if:mode_reservation,nuit_unique|nullable|date|after_or_equal:today',
            'date_arrivee'     => 'required_if:mode_reservation,sejour|nullable|date|after_or_equal:today',
            'date_depart'      => 'required_if:mode_reservation,sejour|nullable|date|after:date_arrivee',
            'nom'              => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'motif'            => 'nullable|string|max:500',
        ]);

        $chambre = Chambre::findOrFail($request->chambre_id);

        // ── Calcul du prix total ──────────────────────────────────
        if ($request->mode_reservation === 'nuit_unique') {
            $prixTotal        = $chambre->prix_nuit;
            $dateReservation  = $request->date_reservation;
            $dateArrivee      = null;
            $dateDepart       = null;
        } else {
            $arrivee          = Carbon::parse($request->date_arrivee);
            $depart           = Carbon::parse($request->date_depart);
            $nbNuits          = $arrivee->diffInDays($depart) ?: 1;
            $prixTotal        = $nbNuits * $chambre->prix_nuit;
            $dateReservation  = null;
            $dateArrivee      = $request->date_arrivee;
            $dateDepart       = $request->date_depart;
        }

        // ── Vérification de disponibilité — seulement sur confirmée ──────
        $conflit = ReservationChambre::where('chambre_id', $chambre->id)
            ->where('statut', 'confirmee')  // ← seulement confirmée bloque
            ->where(function ($q) use ($dateReservation, $dateArrivee, $dateDepart) {
                if ($dateReservation) {
                    $q->where('date_reservation', $dateReservation);
                } else {
                    $q->where(function ($q2) use ($dateArrivee, $dateDepart) {
                        $q2->where('date_arrivee', '<', $dateDepart)
                        ->where('date_depart', '>', $dateArrivee);
                    });
                }
            })->exists();

        if ($conflit) {
            return back()
                ->withInput()
                ->withErrors(['date_reservation' => 'Ces dates sont déjà confirmées pour une autre réservation. Veuillez choisir d\'autres dates.']);
        }

        // ── Création de la réservation ────────────────────────────
        $reservation = ReservationChambre::create([
            'chambre_id'       => $chambre->id,
            'user_id'          => Auth::id(),
            'nom'              => $request->nom,
            'email'            => $request->email,
            'motif'            => $request->motif,
            'date_reservation' => $dateReservation,
            'date_arrivee'     => $dateArrivee,
            'date_depart'      => $dateDepart,
            'prix_total'       => $prixTotal,
            'statut'           => 'en_attente',
        ]);

        NotificationService::clientReservationEnAttente($reservation->load('chambre'));
        NotificationService::gerantsNouvelleReservation($reservation->load('chambre'));
        ActivityLog::log('reservation.creee', Auth::user()->name . ' a réservé ' . $chambre->numero_chambre, 'reservations', '📅', 'info');

        User::where('role', 'gerant')->where('active', true)->get()
            ->each(fn($g) => $g->notify(new NotifInApp(
                '📋', 'Nouvelle réservation',
                Auth::user()->name . ' a réservé ' . $chambre->numero_chambre . ' — ' . number_format($prixTotal, 0, ',', ' ') . ' Ar',
                '/gerant/reservations/' . $reservation->id,
                'info', 'reservations'
            )));

        return redirect()->route('client.reservations.confirmation', $reservation->id);
    }

    /**
     * Page de confirmation après réservation.
     */
    public function confirmation(ReservationChambre $reservation)
    {
        // Sécurité : seul le propriétaire peut voir sa confirmation
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        return view('client.chambres.confirmation', compact('reservation'));
    }

    /**
     * Historique des réservations du client connecté.
     */
    public function mesReservations()
    {
        $reservations = ReservationChambre::with('chambre')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('client.reservations.index', compact('reservations'));
    }

    /**
     * Annulation d'une réservation par le client
     * (uniquement si statut = en_attente).
     */
    public function annuler(ReservationChambre $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if ($reservation->statut !== 'en_attente') {
            return back()->withErrors(['statut' => 'Seules les réservations en attente peuvent être annulées.']);
        }

        $reservation->update(['statut' => 'annulee']);

        NotificationService::gerantsReservationAnnuleeParClient($reservation->load('chambre'));
        ActivityLog::log('reservation.annulee_client', Auth::user()->name . ' a annulé la réservation ' . '#' . str_pad($reservation->id, 6, '0', STR_PAD_LEFT), 'reservations', '❌', 'warning');


        return redirect()->route('client.reservations.index')
            ->with('success', 'Votre réservation a été annulée.');
    }
}
