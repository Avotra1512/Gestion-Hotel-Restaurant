<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'action',
        'description',
        'module',
        'icone',
        'niveau',
        'meta',
        'ip',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ── Relation ──────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper statique : enregistrer une activité ────────────────
    public static function log(
        string $action,
        string $description,
        string $module   = 'general',
        string $icone    = '📋',
        string $niveau   = 'info',
        array  $meta     = [],
        ?int   $userId   = null,
        ?string $role    = null
    ): void {
        try {
            $user = $userId ? User::find($userId) : Auth::user();

            static::create([
                'user_id'     => $user?->id,
                'role'        => $role ?? $user?->role,
                'action'      => $action,
                'description' => $description,
                'module'      => $module,
                'icone'       => $icone,
                'niveau'      => $niveau,
                'meta'        => $meta ?: null,
                'ip'          => Request::ip(),
            ]);
        } catch (\Exception $e) {
            // Ne jamais bloquer l'app pour un log
            logger()->error('ActivityLog error: ' . $e->getMessage());
        }
    }

    // ── Couleur du badge de niveau ────────────────────────────────
    public function couleurNiveau(): string
    {
        return match($this->niveau) {
            'success' => 'bg-green-400/10 text-green-400 border-green-400/20',
            'warning' => 'bg-amber-400/10 text-amber-400 border-amber-400/20',
            'danger'  => 'bg-red-400/10 text-red-400 border-red-400/20',
            default   => 'bg-blue-400/10 text-blue-400 border-blue-400/20',
        };
    }

    // ── Couleur du point de la timeline ──────────────────────────
    public function couleurPoint(): string
    {
        return match($this->niveau) {
            'success' => 'bg-green-400',
            'warning' => 'bg-amber-400',
            'danger'  => 'bg-red-400',
            default   => 'bg-blue-400',
        };
    }
}