<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReservationChambre;
use App\Models\User;
use App\Notifications\RelanceReservationEnAttente;
use Carbon\Carbon;

class RelancerReservationsEnAttente extends Command
{
    protected $signature   = 'misalo:relancer-attente';
    protected $description = 'Notifie le gérant des réservations en attente depuis plus de 24h';

    public function handle(): void
    {
        $reservations = ReservationChambre::with(['user', 'chambre'])
            ->where('statut', 'en_attente')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('Aucune réservation en attente depuis +24h.');
            return;
        }

        // Notifier tous les gérants
        $gerants = User::where('role', 'gerant')->where('active', true)->get();

        foreach ($gerants as $gerant) {
            $gerant->notify(new RelanceReservationEnAttente($reservations));
        }

        $this->info("📧 {$gerants->count()} gérant(s) notifié(s) — {$reservations->count()} réservation(s) en attente.");
    }
}
