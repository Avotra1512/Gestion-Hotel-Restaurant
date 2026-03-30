<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'categorie',
        'description',
        'prix',
        'image',
        'disponible',
    ];

    protected $casts = [
        'prix'       => 'integer',
        'disponible' => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────

    public function commandes()
    {
        return $this->hasMany(CommandeRepasItem::class, 'menu_id');
    }

    // ── Helpers ───────────────────────────────────────────────────

    /**
     * Libellé de la catégorie pour l'affichage.
     */
    public function libelleCategorie(): string
    {
        return match($this->categorie) {
            'entree'         => 'Entrée',
            'plat_principal' => 'Plat principal',
            'dessert'        => 'Dessert',
            'boisson'        => 'Boisson',
            'fast_food'        => 'Fast Food',
            default          => ucfirst($this->categorie),
        };
    }

    /**
     * Couleur Tailwind du badge de catégorie.
     */
    public function couleurCategorie(): string
    {
        return match($this->categorie) {
            'entree'         => 'bg-green-400/10 text-green-400 border-green-400/20',
            'plat_principal' => 'bg-amber-400/10 text-amber-400 border-amber-400/20',
            'dessert'        => 'bg-pink-400/10 text-pink-400 border-pink-400/20',
            'boisson'        => 'bg-blue-400/10 text-blue-400 border-blue-400/20',
            'fast_food'        => 'bg-purple-400/10 text-purple-400 border-purple-400/20',
            default          => 'bg-white/10 text-white/60 border-white/10',
        };
    }
}