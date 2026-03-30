<?php

namespace App\Notifications;

use App\Models\CommandeRepas;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommandeEnPreparation extends Notification
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
            'icone'   => '👨‍🍳',
            'titre'   => 'Commande en préparation',
            'message' => 'Votre commande #' . str_pad($this->commande->id, 6, '0', STR_PAD_LEFT) . ' est en cours de préparation.',
            'lien'    => '/client/commandes-repas',
            'niveau'  => 'info',
            'module'  => 'commandes',
        ];
    }
}