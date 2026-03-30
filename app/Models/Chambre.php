<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chambre extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_chambre',
        'type_chambre',
        'prix_nuit',
        'equipements',
        'statut',
        'description',
        'image',
    ];

    protected $casts = [
        'equipements' => 'array',
        'prix_nuit'   => 'integer',
    ];

    public function reservations()
    {
        return $this->hasMany(ReservationChambre::class, 'chambre_id');
    }
}
