<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReservationChambre extends Model
{
    use HasFactory;

    protected $table = 'reservations_chambre';

    protected $fillable = [
        'chambre_id',
        'user_id',
        'date_reservation',
        'date_arrivee',
        'date_depart',
        'prix_total',
        'nom',
        'email',
        'motif',
        'statut',
    ];

    protected $casts = [
        'date_reservation' => 'date',
        'date_arrivee'     => 'date',
        'date_depart'      => 'date',
        'prix_total'       => 'integer',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function chambre()
    {
        return $this->belongsTo(Chambre::class, 'chambre_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    /**
     * Retourne le nombre de nuits du séjour.
     */
    public function nombreNuits(): int
    {
        if ($this->date_reservation) {
            return 1;
        }
        if ($this->date_arrivee && $this->date_depart) {
            return $this->date_arrivee->diffInDays($this->date_depart) ?: 1;
        }
        return 1;
    }

    /**
     * Retourne la date d'affichage principale (arrivée ou nuit unique).
     */
    public function dateAffichage(): ?Carbon
    {
        return $this->date_reservation ?? $this->date_arrivee;
    }

    /**
     * Libellé du statut pour l'affichage.
     */
    public function libelleStatut(): string
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'confirmee'  => 'Confirmée',
            'payee'      => 'Payée',
            'terminee'   => 'Terminée',
            'annulee'    => 'Annulée',
            default      => ucfirst($this->statut),
        };
    }

    /**
     * Couleur Tailwind du badge de statut.
     */
    public function couleurStatut(): string
    {
        return match($this->statut) {
            'en_attente' => 'bg-amber-400/10 text-amber-400 border-amber-400/20',
            'confirmee'  => 'bg-blue-400/10 text-blue-400 border-blue-400/20',
            'payee'      => 'bg-green-400/10 text-green-400 border-green-400/20',
            'terminee'   => 'bg-neutral-400/10 text-neutral-400 border-neutral-400/20',
            'annulee'    => 'bg-red-400/10 text-red-400 border-red-400/20',
            default      => 'bg-white/10 text-white/60 border-white/10',
        };
    }
}
