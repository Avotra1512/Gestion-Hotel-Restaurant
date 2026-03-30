<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use Illuminate\Support\Facades\Auth;

class FideliteController extends Controller
{
    /**
     * Calcule les points et le niveau de fidélité du client.
     */
    public function index()
    {
        $user = Auth::user();

        // ── Calcul des points ─────────────────────────────────────
        $reservationsPagées = ReservationChambre::with('chambre')
            ->where('user_id', $user->id)
            ->whereIn('statut', ['payee', 'terminee'])
            ->get();

        $commandesLivrees = CommandeRepas::with('items.menu')
            ->where('user_id', $user->id)
            ->where('statut', 'livree')
            ->get();

        // 10 points par nuit, 5 points par commande livrée
        $pointsChambres   = $reservationsPagées->sum(fn($r) => $r->nombreNuits() * 10);
        $pointsRestaurant = $commandesLivrees->count() * 5;
        $pointsTotal      = $pointsChambres + $pointsRestaurant;

        // ── Niveau ────────────────────────────────────────────────
        $niveau = $this->getNiveau($pointsTotal);

        // ── Progression vers le niveau suivant ────────────────────
        $progression = $this->getProgression($pointsTotal);

        // ── Historique des gains de points ────────────────────────
        $historiquePoints = collect();

        foreach ($reservationsPagées as $r) {
            $pts = $r->nombreNuits() * 10;
            $historiquePoints->push([
                'type'        => 'reservation',
                'icone'       => '🛏️',
                'description' => 'Séjour — ' . ($r->chambre?->numero_chambre ?? '—') . ' (' . $r->nombreNuits() . ' nuit(s))',
                'points'      => '+' . $pts,
                'date'        => $r->updated_at,
                'couleur'     => 'text-amber-400',
            ]);
        }

        foreach ($commandesLivrees as $c) {
            $historiquePoints->push([
                'type'        => 'commande',
                'icone'       => '🍽️',
                'description' => 'Commande restaurant #' . str_pad($c->id, 6, '0', STR_PAD_LEFT),
                'points'      => '+5',
                'date'        => $c->updated_at,
                'couleur'     => 'text-blue-400',
            ]);
        }

        $historiquePoints = $historiquePoints->sortByDesc('date')->values();

        // ── Stats résumé ──────────────────────────────────────────
        $stats = [
            'nb_nuits'      => $reservationsPagées->sum(fn($r) => $r->nombreNuits()),
            'nb_sejours'    => $reservationsPagées->count(),
            'nb_commandes'  => $commandesLivrees->count(),
            'total_depense' => $reservationsPagées->sum('prix_total') + $commandesLivrees->sum('total'),
        ];

        return view('client.fidelite.index', compact(
            'pointsTotal', 'pointsChambres', 'pointsRestaurant',
            'niveau', 'progression', 'historiquePoints', 'stats'
        ));
    }

    // ── Helpers privés ────────────────────────────────────────────

    private function getNiveau(int $points): array
    {
        return match(true) {
            $points >= 3000 => [
                'nom'      => 'Diamant',
                'icone'    => '💎',
                'couleur'  => 'text-cyan-400',
                'bordure'  => 'border-cyan-400/30',
                'bg'       => 'bg-cyan-400/10',
                'gradient' => 'from-cyan-400/20 to-cyan-300/5',
                'remise'   => 15,
                'desc'     => 'Service VIP + 15% de remise sur vos réservations',
            ],
            $points >= 1500 => [
                'nom'      => 'Or',
                'icone'    => '🥇',
                'couleur'  => 'text-amber-400',
                'bordure'  => 'border-amber-400/30',
                'bg'       => 'bg-amber-400/10',
                'gradient' => 'from-amber-400/20 to-amber-300/5',
                'remise'   => 10,
                'desc'     => 'Priorité de confirmation + 10% de remise',
            ],
            $points >= 500 => [
                'nom'      => 'Argent',
                'icone'    => '🥈',
                'couleur'  => 'text-neutral-300',
                'bordure'  => 'border-neutral-300/30',
                'bg'       => 'bg-neutral-300/10',
                'gradient' => 'from-neutral-300/20 to-neutral-200/5',
                'remise'   => 5,
                'desc'     => '5% de remise sur vos prochaines réservations',
            ],
            default => [
                'nom'      => 'Bronze',
                'icone'    => '🥉',
                'couleur'  => 'text-orange-400',
                'bordure'  => 'border-orange-400/30',
                'bg'       => 'bg-orange-400/10',
                'gradient' => 'from-orange-400/20 to-orange-300/5',
                'remise'   => 0,
                'desc'     => 'Continuez à réserver pour débloquer des avantages',
            ],
        };
    }

    private function getProgression(int $points): array
    {
        $paliers = [500, 1500, 3000];

        foreach ($paliers as $palier) {
            if ($points < $palier) {
                $precedent  = match($palier) { 500 => 0, 1500 => 500, 3000 => 1500, default => 0 };
                $restant    = $palier - $points;
                $pourcentage = min(100, round((($points - $precedent) / ($palier - $precedent)) * 100));

                $niveauSuivant = match($palier) {
                    500  => ['nom' => 'Argent',  'icone' => '🥈'],
                    1500 => ['nom' => 'Or',      'icone' => '🥇'],
                    3000 => ['nom' => 'Diamant', 'icone' => '💎'],
                };

                return [
                    'prochain'     => $niveauSuivant,
                    'points_requis'=> $palier,
                    'restant'      => $restant,
                    'pourcentage'  => $pourcentage,
                    'max_atteint'  => false,
                ];
            }
        }

        return ['max_atteint' => true, 'pourcentage' => 100];
    }
}