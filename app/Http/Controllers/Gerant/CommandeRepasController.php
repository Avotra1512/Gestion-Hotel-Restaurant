<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CommandeRepas;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Notifications\NotifInApp;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class CommandeRepasController extends Controller
{
    /**
     * Liste toutes les commandes avec filtres et recherche.
     */
    public function index(Request $request)
    {
        $query = CommandeRepas::with(['user', 'items.menu']);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")
                                      ->orWhere('email', 'like', "%$s%"));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $commandes = $query->latest()->paginate(15)->withQueryString();

        $compteurs = [
            'tous'           => CommandeRepas::count(),
            'en_attente'     => CommandeRepas::where('statut', 'en_attente')->count(),
            'en_preparation' => CommandeRepas::where('statut', 'en_preparation')->count(),
            'prete'          => CommandeRepas::where('statut', 'prete')->count(),
            'livree'         => CommandeRepas::where('statut', 'livree')->count(),
            'annulee'        => CommandeRepas::where('statut', 'annulee')->count(),
        ];

        // Revenus du jour
        $revenuJour = CommandeRepas::where('statut', 'livree')
            ->whereDate('updated_at', Carbon::today())
            ->sum('total');

        return view('gerant.commandes.index', compact('commandes', 'compteurs', 'revenuJour'));
    }

    /**
     * Détail d'une commande.
     */
    public function show(CommandeRepas $commande)
    {
        $commande->load(['user', 'items.menu']);
        return view('gerant.commandes.show', compact('commande'));
    }

    /**
     * Mettre à jour le statut d'une commande.
     */
    public function updateStatut(Request $request, CommandeRepas $commande)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_preparation,prete,livree,annulee',
        ]);

        $commande->update(['statut' => $request->statut]);

        $message = match($request->statut) {
            'en_preparation' => '👨‍🍳 Commande passée en préparation.',
            'prete'          => '✅ Commande marquée comme prête.',
            'livree'         => '🍽️ Commande livrée avec succès.',
            'annulee'        => '❌ Commande annulée.',
            default          => 'Statut mis à jour.',
        };

        return back()->with('success', $message);
    }

    /**
     * Raccourcis rapides de transition de statut.
     */
    public function passerEnPreparation(CommandeRepas $commande)
    {
        $commande->update(['statut' => 'en_preparation']);
        NotificationService::clientCommandeEnPreparation($commande);
        ActivityLog::log('commande.preparation', Auth::user()->name . ' → Commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . ' en préparation', 'commandes', '👨‍🍳', 'info');

        if ($commande->user) {
            $commande->user->notify(new NotifInApp('👨‍🍳', 'Commande en préparation',
                'Votre commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . ' est en cours de préparation.',
                '/client/commandes-repas', 'info', 'commandes'));
        }
                return back()->with('success', '👨‍🍳 Commande passée en préparation.');
    }

    public function marquerPrete(CommandeRepas $commande)
    {
        $commande->update(['statut' => 'prete']);
        NotificationService::clientCommandePrete($commande);
        ActivityLog::log('commande.prete', Auth::user()->name . ' → Commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . ' prête', 'commandes', '🔔', 'success');

        if ($commande->user) {
            $commande->user->notify(new NotifInApp('🍽️', 'Commande prête !',
                'Votre commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . ' est prête à être servie.',
                '/client/commandes-repas', 'success', 'commandes'));
        }
        return back()->with('success', '✅ Commande prête à être servie.');
    }

    public function marquerLivree(CommandeRepas $commande)
    {
        $commande->update(['statut' => 'livree']);
        NotificationService::clientCommandeLivree($commande);
        NotificationService::adminsCommandeLivree($commande);
        ActivityLog::log('commande.livree', Auth::user()->name . ' → Commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . ' livrée', 'commandes', '✅', 'success');

        if ($commande->user) {
            $commande->user->notify(new NotifInApp('✅', 'Commande livrée',
                'Votre commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . ' a été livrée.',
                '/client/factures', 'success', 'commandes'));
        }
        return back()->with('success', '🍽️ Commande livrée.');
    }

    /**
     * Générer la facture PDF d'une commande.
     */
    public function facturePdf(CommandeRepas $commande)
    {
        $commande->load(['user', 'items.menu']);

        $pdf = Pdf::loadView('gerant.commandes.facture-pdf', compact('commande'))
                  ->setPaper('a4', 'portrait');

        $nom = 'facture_commande_MISALO_' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nom);
    }
}
