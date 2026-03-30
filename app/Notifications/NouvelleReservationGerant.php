<?php

namespace App\Notifications;

use App\Models\ReservationChambre;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NouvelleReservationGerant extends Notification
{
    use Queueable;

    public function __construct(public ReservationChambre $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ref    = '#' . str_pad($this->reservation->id, 6, '0', STR_PAD_LEFT);
        $client = $this->reservation->nom;
        return (new MailMessage)
            ->subject("📋 Nouvelle réservation {$ref} — {$client}")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("Une nouvelle réservation vient d'être enregistrée.")
            ->line("**Client :** {$client}")
            ->line("**Chambre :** " . ($this->reservation->chambre?->numero_chambre ?? '—'))
            ->line("**Montant :** " . number_format($this->reservation->prix_total, 0, ',', ' ') . ' Ar')
            ->action('Traiter la réservation', url('/gerant/reservations/' . $this->reservation->id))
            ->salutation("Système MISALO 🤖");
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'   => '📋',
            'titre'   => 'Nouvelle réservation',
            'message' => $this->reservation->nom . ' a réservé la ' . ($this->reservation->chambre?->numero_chambre ?? '—') . ' — ' . number_format($this->reservation->prix_total, 0, ',', ' ') . ' Ar',
            'lien'    => '/gerant/reservations/' . $this->reservation->id,
            'niveau'  => 'info',
            'module'  => 'reservations',
        ];
    }
}