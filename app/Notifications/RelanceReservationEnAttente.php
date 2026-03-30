<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class RelanceReservationEnAttente extends Notification
{
    use Queueable;

    public function __construct(
        public Collection $reservations
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nb      = $this->reservations->count();
        $message = (new MailMessage)
            ->subject("⚠️ {$nb} réservation(s) en attente depuis +24h — MISALO")
            ->greeting("Bonjour {$notifiable->name},")
            ->line("**{$nb} réservation(s)** sont en attente depuis plus de **24 heures** et nécessitent votre attention.")
            ->line("---");

        foreach ($this->reservations->take(5) as $res) {
            $ref     = '#' . str_pad($res->id, 6, '0', STR_PAD_LEFT);
            $client  = $res->nom;
            $chambre = $res->chambre?->numero_chambre ?? '—';
            $depuis  = $res->created_at->diffForHumans();
            $message->line("📋 **{$ref}** — {$client} — {$chambre} — En attente {$depuis}");
        }

        if ($nb > 5) {
            $message->line("... et " . ($nb - 5) . " autre(s).");
        }

        return $message
            ->line("---")
            ->action('Gérer les réservations', url('/gerant/reservations?statut=en_attente'))
            ->salutation("Système MISALO 🤖");
    }
}