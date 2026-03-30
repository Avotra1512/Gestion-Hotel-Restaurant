<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeRepasItem extends Model
{
    use HasFactory;

    protected $table = 'commande_repas_items';

    protected $fillable = [
        'commande_repas_id',
        'menu_id',
        'quantite',
        'prix_unitaire',
        'sous_total',
    ];

    protected $casts = [
        'quantite'      => 'integer',
        'prix_unitaire' => 'integer',
        'sous_total'    => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────

    public function commande()
    {
        return $this->belongsTo(CommandeRepas::class, 'commande_repas_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}