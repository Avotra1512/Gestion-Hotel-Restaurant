<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\Chambre;
use App\Services\NotificationService;
use Carbon\Carbon;

class MisaloRappelsQuotidiens extends Command
{
    protected $signature   = 'misalo:rappels-quotidiens';
    protected $description = 'Envoie les rappels et alertes quotidiennes à tous les acteurs';

    public function handle(): void
    {
        // ── 1. Réservations en attente depuis +24h ─────────────────
        $nbResAttente = ReservationChambre::where('statut', 'en_attente')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->count();

        if ($nbResAttente > 0) {
            NotificationService::gerantsRappelReservationsEnAttente($nbResAttente);
            $this->info("📋 {$nbResAttente} réservation(s) en attente → gérants notifiés.");
        }

        // ── 2. Commandes en attente depuis +1h ─────────────────────
        $nbCmdAttente = CommandeRepas::whereIn('statut', ['en_attente', 'en_preparation'])
            ->where('created_at', '<', Carbon::now()->subHour())
            ->count();

        if ($nbCmdAttente > 0) {
            NotificationService::gerantsRappelCommandesEnAttente($nbCmdAttente);
            $this->info("🍽️ {$nbCmdAttente} commande(s) en attente → gérants notifiés.");
        }

        // ── 3. Réservations dépassées non traitées ─────────────────
        $nbDepassees = ReservationChambre::whereIn('statut', ['en_attente', 'confirmee'])
            ->where(function ($q) {
                $q->whereDate('date_reservation', '<', today())
                  ->orWhereDate('date_depart', '<', today());
            })->count();

        if ($nbDepassees > 0) {
            NotificationService::gerantsReservationsDepassees($nbDepassees);
            $this->info("🚨 {$nbDepassees} réservation(s) dépassée(s) → gérants notifiés.");
        }

        // ── 4. Taux d'occupation faible (< 30%) ───────────────────
        $totalChambres = Chambre::where('statut', '!=', 'hors_service')->count();
        if ($totalChambres > 0) {
            $occupees = Chambre::where('statut', 'occupee')->count();
            $taux     = round(($occupees / $totalChambres) * 100);

            if ($taux < 30) {
                NotificationService::adminsTauxOccupationFaible($taux);
                $this->info("📉 Taux d'occupation {$taux}% → admins notifiés.");
            }
        }

        // ── 5. Surveillance annulations excessives ─────────────────
        $annulationsRes = ReservationChambre::where('statut', 'annulee')
            ->where('updated_at', '>=', Carbon::now()->subWeek())
            ->count();
        if ($annulationsRes >= 5) {
            NotificationService::adminsAlerteAnnulations($annulationsRes, 'réservations');
        }

        $annulationsCmd = CommandeRepas::where('statut', 'annulee')
            ->where('updated_at', '>=', Carbon::now()->subWeek())
            ->count();
        if ($annulationsCmd >= 10) {
            NotificationService::adminsAlerteAnnulations($annulationsCmd, 'commandes');
        }

        $this->info('✅ Rappels quotidiens envoyés.');
    }
}
