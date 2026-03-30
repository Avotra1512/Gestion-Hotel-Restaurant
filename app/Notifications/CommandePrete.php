<?php

namespace App\Notifications;

use App\Models\CommandeRepas;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommandePrete extends Notification
{
    use Queueable;

    public function __construct(public CommandeRepas $commande) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref = '#' . str_pad($this->commande->id, 6, '0', STR_PAD_LEFT);
        return (new MailMessage)
            ->subject("🍽️ Votre commande est prête — MISALO {$ref}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre commande **{$ref}** est **prête** et sera servie dans quelques instants.")
            ->line("**💰 Montant :** " . number_format($this->commande->total, 0, ',', ' ') . ' Ar')
            ->action('Voir ma commande', url('/client/commandes-repas'))
            ->salutation("L'équipe MISALO 🍽️");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'   => '🍽️',
            'titre'   => 'Commande prête !',
            'message' => 'Votre commande #' . str_pad($this->commande->id, 6, '0', STR_PAD_LEFT) . ' est prête à être servie.',
            'lien'    => '/client/commandes-repas',
            'niveau'  => 'success',
            'module'  => 'commandes',
        ];
    }
}