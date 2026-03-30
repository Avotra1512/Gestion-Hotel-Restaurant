<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Services\NotificationService;
use Carbon\Carbon;

class MisaloRapportRevenus extends Command
{
    protected $signature   = 'misalo:rapport-revenus';
    protected $description = 'Envoie le rapport des revenus d\'hier aux admins (chaque matin à 08h)';

    public function handle(): void
    {
        $hier = Carbon::yesterday();

        $revenus_chambres = ReservationChambre::where('statut', 'payee')
            ->whereDate('updated_at', $hier)
            ->sum('prix_total');

        $revenus_restaurant = CommandeRepas::where('statut', 'livree')
            ->whereDate('updated_at', $hier)
            ->sum('total');

        NotificationService::adminsRapportQuotidienRevenus(
            (int) $revenus_chambres,
            (int) $revenus_restaurant
        );

        $this->info('📊 Rapport revenus de ' . $hier->format('d/m/Y') . ' envoyé aux admins.');
    }
}
