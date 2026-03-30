<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active'  => 'boolean', 
        ];
    }
    public function reservationChambres()
    {
        return $this->hasMany(\App\Models\ReservationChambre::class, 'user_id');
    }
    // Helper badge rôle — ajouter dans la classe
    public function couleurRole(): string
    {
        return match($this->role) {
            'admin'  => 'bg-red-400/10 text-red-400 border-red-400/20',
            'gerant' => 'bg-purple-400/10 text-purple-400 border-purple-400/20',
            'client' => 'bg-blue-400/10 text-blue-400 border-blue-400/20',
            default  => 'bg-white/10 text-white/60 border-white/10',
        };
    }

    public function libelleRole(): string
    {
        return match($this->role) {
            'admin'  => 'Administrateur',
            'gerant' => 'Gérant',
            'client' => 'Client',
            default  => ucfirst($this->role),
        };
    }
}
