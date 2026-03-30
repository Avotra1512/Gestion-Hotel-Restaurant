<?php

namespace App\Notifications;

use App\Models\ReservationChambre;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaiementValide extends Notification
{
    use Queueable;

    public function __construct(public ReservationChambre $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref = '#' . str_pad($this->reservation->id, 6, '0', STR_PAD_LEFT);
        return (new MailMessage)
            ->subject("💰 Paiement validé — MISALO {$ref}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Le paiement de votre réservation **{$ref}** a été **validé**.")
            ->line("**💰 Montant :** " . number_format($this->reservation->prix_total, 0, ',', ' ') . ' Ar')
            ->line("Votre facture PDF est disponible dans votre espace client.")
            ->action('Télécharger ma facture', url('/client/factures'))
            ->salutation("L'équipe MISALO 🏨");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'   => '💰',
            'titre'   => 'Paiement validé',
            'message' => 'Le paiement de votre réservation #' . str_pad($this->reservation->id, 6, '0', STR_PAD_LEFT) . ' a été validé. Facture disponible.',
            'lien'    => '/client/factures',
            'niveau'  => 'success',
            'module'  => 'reservations',
        ];
    }
}