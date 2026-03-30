<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeRepas extends Model
{
    use HasFactory;

    protected $table = 'commandes_repas';

    protected $fillable = [
        'user_id',
        'nom',
        'email',
        'statut',
        'total',
        'note',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CommandeRepasItem::class, 'commande_repas_id');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function libelleStatut(): string
    {
        return match($this->statut) {
            'en_attente'     => 'En attente',
            'en_preparation' => 'En préparation',
            'prete'          => 'Prête',
            'livree'         => 'Livrée',
            'annulee'        => 'Annulée',
            default          => ucfirst($this->statut),
        };
    }

    public function couleurStatut(): string
    {
        return match($this->statut) {
            'en_attente'     => 'bg-amber-400/10 text-amber-400 border-amber-400/20',
            'en_preparation' => 'bg-blue-400/10 text-blue-400 border-blue-400/20',
            'prete'          => 'bg-purple-400/10 text-purple-400 border-purple-400/20',
            'livree'         => 'bg-green-400/10 text-green-400 border-green-400/20',
            'annulee'        => 'bg-red-400/10 text-red-400 border-red-400/20',
            default          => 'bg-white/10 text-white/60 border-white/10',
        };
    }

    public function iconeStatut(): string
    {
        return match($this->statut) {
            'en_attente'     => '⏳',
            'en_preparation' => '👨‍🍳',
            'prete'          => '✅',
            'livree'         => '🍽️',
            'annulee'        => '❌',
            default          => '•',
        };
    }
}