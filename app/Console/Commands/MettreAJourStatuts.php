<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReservationChambre;
use App\Models\Chambre;
use Carbon\Carbon;

class MettreAJourStatuts extends Command
{
    protected $signature   = 'misalo:update-statuts';
    protected $description = 'Met à jour automatiquement les réservations terminées et libère les chambres';

    public function handle(): void
    {
        $today = Carbon::today();
        $nb    = 0;

        $reservations = ReservationChambre::whereIn('statut', ['confirmee', 'payee'])
            ->where(function ($q) use ($today) {
                $q->whereDate('date_reservation', '<', $today)
                  ->orWhereDate('date_depart', '<', $today);
            })
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->update(['statut' => 'terminee']);
            $nb++;

            $chambre = $reservation->chambre;
            if ($chambre && $chambre->statut === 'occupee') {
                $autresActives = ReservationChambre::where('chambre_id', $chambre->id)
                    ->whereIn('statut', ['confirmee', 'payee'])
                    ->where('id', '!=', $reservation->id)
                    ->exists();

                if (!$autresActives) {
                    $chambre->update(['statut' => 'disponible']);
                }
            }
        }

        $this->info("✅ {$nb} réservation(s) passée(s) en terminée.");
    }
}
