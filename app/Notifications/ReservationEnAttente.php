<?php

namespace App\Notifications;

use App\Models\ReservationChambre;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationEnAttente extends Notification
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
            ->subject("⏳ Réservation reçue — MISALO {$ref}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Votre réservation **{$ref}** a bien été enregistrée et est en attente de confirmation.")
            ->line("**🛏️ Chambre :** " . ($this->reservation->chambre?->numero_chambre ?? '—'))
            ->line("**💰 Montant :** " . number_format($this->reservation->prix_total, 0, ',', ' ') . ' Ar')
            ->line("Vous recevrez un email dès que notre équipe aura confirmé votre réservation.")
            ->action('Voir ma réservation', url('/client/reservations'))
            ->salutation("L'équipe MISALO 🏨");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'       => '⏳',
            'titre'       => 'Réservation enregistrée',
            'message'     => 'Votre réservation #' . str_pad($this->reservation->id, 6, '0', STR_PAD_LEFT) . ' est en attente de confirmation.',
            'lien'        => '/client/reservations',
            'niveau'      => 'info',
            'module'      => 'reservations',
        ];
    }
}