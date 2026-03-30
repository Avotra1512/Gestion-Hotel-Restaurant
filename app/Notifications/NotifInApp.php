<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifInApp extends Notification
{
    use Queueable;

    public function __construct(
        public string $icone,
        public string $titre,
        public string $message,
        public string $lien    = '#',
        public string $niveau  = 'info',
        public string $module  = 'general'
    ) {}

    // Seulement en base de données — pas d'email
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icone'   => $this->icone,
            'titre'   => $this->titre,
            'message' => $this->message,
            'lien'    => $this->lien,
            'niveau'  => $this->niveau,
            'module'  => $this->module,
        ];
    }
}
