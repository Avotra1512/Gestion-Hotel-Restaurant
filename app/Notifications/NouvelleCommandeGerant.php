<?php

namespace App\Notifications;

use App\Models\CommandeRepas;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NouvelleCommandeGerant extends Notification
{
    use Queueable;

    public function __construct(public CommandeRepas $commande) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'   => '🍽️',
            'titre'   => 'Nouvelle commande restaurant',
            'message' => $this->commande->nom . ' a commandé ' . $this->commande->items->count() . ' plat(s) — ' . number_format($this->commande->total, 0, ',', ' ') . ' Ar',
            'lien'    => '/gerant/commandes/' . $this->commande->id,
            'niveau'  => 'info',
            'module'  => 'commandes',
        ];
    }
}