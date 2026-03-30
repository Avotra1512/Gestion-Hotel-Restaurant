<?php

namespace App\Notifications;

use App\Models\CommandeRepas;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CommandeLivree extends Notification
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
            ->subject("✅ Commande livrée — MISALO {$ref}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre commande **{$ref}** a été **livrée**. Bon appétit !")
            ->line("**💰 Montant :** " . number_format($this->commande->total, 0, ',', ' ') . ' Ar')
            ->line("Votre facture PDF est disponible dans votre espace client.")
            ->action('Télécharger ma facture', url('/client/factures'))
            ->salutation("L'équipe MISALO 🍽️");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'   => '✅',
            'titre'   => 'Commande livrée',
            'message' => 'Votre commande #' . str_pad($this->commande->id, 6, '0', STR_PAD_LEFT) . ' a été livrée. Facture disponible.',
            'lien'    => '/client/factures',
            'niveau'  => 'success',
            'module'  => 'commandes',
        ];
    }
}