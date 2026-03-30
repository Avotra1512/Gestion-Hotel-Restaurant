<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\CommandeRepas;
use App\Models\CommandeRepasItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ActivityLog;
use App\Services\NotificationService;

class CommandeRepasController extends Controller
{
    /**
     * Affiche le menu par catégorie + panier en session.
     */
    public function index(Request $request)
    {
        // Récupérer tous les menus disponibles groupés par catégorie
        $menus = Menu::where('disponible', true)
            ->orderBy('categorie')
            ->orderBy('nom')
            ->get()
            ->groupBy('categorie');

        // Ordre d'affichage des catégories
        $ordreCategories = ['entree', 'plat_principal', 'dessert', 'fast_food', 'boisson'];

        // Panier depuis la session
        $panier = session('panier', []);
        $totalPanier = collect($panier)->sum('sous_total');
        $nbArticles  = collect($panier)->sum('quantite');

        return view('client.restaurant.index', compact(
            'menus', 'ordreCategories', 'panier', 'totalPanier', 'nbArticles'
        ));
    }

    /**
     * Ajouter un plat au panier (session).
     */
    public function ajouterAuPanier(Request $request)
    {
        $request->validate([
            'menu_id'  => 'required|exists:menus,id',
            'quantite' => 'required|integer|min:1|max:20',
        ]);

        $menu = Menu::findOrFail($request->menu_id);

        if (!$menu->disponible) {
            return back()->withErrors(['menu' => 'Ce plat n\'est plus disponible.']);
        }

        $panier = session('panier', []);
        $key    = 'menu_' . $menu->id;

        if (isset($panier[$key])) {
            // Augmenter la quantité si déjà dans le panier
            $panier[$key]['quantite']  += $request->quantite;
            $panier[$key]['sous_total'] = $panier[$key]['quantite'] * $menu->prix;
        } else {
            // Ajouter nouveau
            $panier[$key] = [
                'menu_id'      => $menu->id,
                'nom'          => $menu->nom,
                'categorie'    => $menu->categorie,
                'prix_unitaire'=> $menu->prix,
                'quantite'     => $request->quantite,
                'sous_total'   => $menu->prix * $request->quantite,
                'image'        => $menu->image,
            ];
        }

        session(['panier' => $panier]);

        return back()->with('success', '"' . $menu->nom . '" ajouté au panier.');
    }

    /**
     * Modifier la quantité d'un item dans le panier.
     */
    public function modifierQuantite(Request $request)
    {
        $request->validate([
            'key'      => 'required|string',
            'quantite' => 'required|integer|min:1|max:20',
        ]);

        $panier = session('panier', []);
        $key    = $request->key;

        if (isset($panier[$key])) {
            $panier[$key]['quantite']   = $request->quantite;
            $panier[$key]['sous_total'] = $panier[$key]['prix_unitaire'] * $request->quantite;
            session(['panier' => $panier]);
        }

        return back();
    }

    /**
     * Supprimer un item du panier.
     */
    public function supprimerDuPanier(Request $request)
    {
        $request->validate(['key' => 'required|string']);

        $panier = session('panier', []);
        unset($panier[$request->key]);
        session(['panier' => $panier]);

        return back()->with('success', 'Article retiré du panier.');
    }

    /**
     * Vider tout le panier.
     */
    public function viderPanier()
    {
        session()->forget('panier');
        return back()->with('success', 'Panier vidé.');
    }

    /**
     * Page de récapitulatif avant confirmation.
     */
    public function recapitulatif()
    {
        $panier = session('panier', []);

        if (empty($panier)) {
            return redirect()->route('client.restaurant.index')
                ->withErrors(['panier' => 'Votre panier est vide.']);
        }

        $totalPanier = collect($panier)->sum('sous_total');

        return view('client.restaurant.recapitulatif', compact('panier', 'totalPanier'));
    }

    /**
     * Confirmer et enregistrer la commande.
     */
    public function confirmer(Request $request)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $panier = session('panier', []);

        if (empty($panier)) {
            return redirect()->route('client.restaurant.index')
                ->withErrors(['panier' => 'Votre panier est vide.']);
        }

        $total = collect($panier)->sum('sous_total');

        DB::transaction(function () use ($panier, $total, $request) {
            // Créer la commande
            $commande = CommandeRepas::create([
                'user_id' => Auth::id(),
                'nom'     => Auth::user()->name,
                'email'   => Auth::user()->email,
                'statut'  => 'en_attente',
                'total'   => $total,
                'note'    => $request->note,
            ]);

            // Créer les items
            foreach ($panier as $item) {
                CommandeRepasItem::create([
                    'commande_repas_id' => $commande->id,
                    'menu_id'           => $item['menu_id'],
                    'quantite'          => $item['quantite'],
                    'prix_unitaire'     => $item['prix_unitaire'],
                    'sous_total'        => $item['sous_total'],
                ]);
            }

            NotificationService::clientCommandeEnregistree($commande);
            NotificationService::gerantsNouvelleCommande($commande->load('items'));
            ActivityLog::log('commande.creee', Auth::user()->name . ' a passé une commande restaurant', 'commandes', '🍽️', 'info');

            // Vider le panier
            session()->forget('panier');

            // Stocker l'id pour la redirection
            session(['derniere_commande_id' => $commande->id]);
        });

        $commandeId = session('derniere_commande_id');

        return redirect()->route('client.restaurant.confirmation', $commandeId);
    }

    /**
     * Page de confirmation après commande.
     */
    public function confirmation(CommandeRepas $commande)
    {
        if ($commande->user_id !== Auth::id()) {
            abort(403);
        }

        $commande->load('items.menu');

        return view('client.restaurant.confirmation', compact('commande'));
    }

    /**
     * Historique des commandes du client.
     */
    public function mesCommandes()
    {
        $commandes = CommandeRepas::with('items.menu')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('client.restaurant.mes-commandes', compact('commandes'));
    }

    /**
     * Annuler une commande (seulement si en_attente).
     */
    public function annuler(CommandeRepas $commande)
    {
        if ($commande->user_id !== Auth::id()) {
            abort(403);
        }

        if ($commande->statut !== 'en_attente') {
            return back()->withErrors(['statut' => 'Seules les commandes en attente peuvent être annulées.']);
        }

        $commande->update(['statut' => 'annulee']);

        NotificationService::gerantsCommandeAnnuleeParClient($commande);
        ActivityLog::log('commande.annulee_client', Auth::user()->name . ' a annulé la commande #' . str_pad($commande->id, 6, '0', STR_PAD_LEFT), 'commandes', '❌', 'warning');

        return back()->with('success', 'Commande annulée avec succès.');
    }

    /**
     * Télécharger la facture PDF d'une commande.
     */
    public function facturePdf(CommandeRepas $commande)
    {
        if ($commande->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($commande->statut, ['livree', 'prete'])) {
            return back()->withErrors(['statut' => 'La facture est disponible uniquement pour les commandes livrées.']);
        }

        $commande->load('items.menu');

        $pdf = Pdf::loadView('client.restaurant.facture-pdf', compact('commande'))
                  ->setPaper('a4', 'portrait');

        $nom = 'facture_commande_MISALO_' . str_pad($commande->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($nom);
    }
}
