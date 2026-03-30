<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * Liste tous les menus avec filtres.
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('disponible')) {
            $query->where('disponible', $request->disponible === '1');
        }

        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        $menus = $query->orderBy('categorie')->orderBy('nom')->get();

        // Stats pour les cartes du haut
        $stats = [
            'total'        => Menu::count(),
            'disponibles'  => Menu::where('disponible', true)->count(),
            'indisponibles'=> Menu::where('disponible', false)->count(),
            'categories'   => Menu::distinct('categorie')->count('categorie'),
        ];

        return view('admin.menus.index', compact('menus', 'stats'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('admin.menus.create');
    }

    /**
     * Enregistre un nouveau menu.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:255',
            'categorie'   => ['required', Rule::in(['entree','plat_principal','dessert','boisson','fast_food'])],
            'description' => 'nullable|string',
            'prix'        => 'required|integer|min:0',
            'disponible'  => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['disponible'] = $request->has('disponible');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Menu::create($data);

        NotificationService::tousClientsNouveauMenu($menu);
        NotificationService::gerantsMenuModifieParAdmin($menu);
        ActivityLog::log('menu.cree', Auth::user()->name . ' a ajouté le plat "' . $menu->nom . '"', 'menus', '🍽️', 'success');

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu "' . $data['nom'] . '" ajouté avec succès.');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    /**
     * Met à jour un menu existant.
     */
    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:255',
            'categorie'   => ['required', Rule::in(['entree','plat_principal','dessert','boisson','fasr_food'])],
            'description' => 'nullable|string',
            'prix'        => 'required|integer|min:0',
            'disponible'  => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['disponible'] = $request->has('disponible');

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menus', 'public');
        } else {
            unset($data['image']);
        }

        $menu->update($data);

        NotificationService::gerantsMenuModifieParAdmin($menu);
        ActivityLog::log('menu.modifie', Auth::user()->name . ' a modifié le plat "' . $menu->nom . '"', 'menus', '✏️', 'info');

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu "' . $menu->nom . '" mis à jour avec succès.');
    }

    /**
     * Supprime un menu.
     */
    public function destroy(Menu $menu)
    {
        $nom = $menu->nom;

        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        ActivityLog::log('menu.supprime', 'Plat "' . $nom . '" supprimé', 'menus', '🗑️', 'danger');

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu "' . $nom . '" supprimé avec succès.');
    }

    /**
     * Bascule rapide disponible/indisponible (appelé via PATCH).
     */
    public function toggleDisponible(Menu $menu)
    {
        $menu->update(['disponible' => !$menu->disponible]);

        NotificationService::adminsMenuToggleParGerant($menu, Auth::user());
        ActivityLog::log('menu.toggle', Auth::user()->name . ' a ' . ($menu->disponible ? 'activé' : 'désactivé') . ' "' . $menu->nom . '"', 'menus', '🔄', 'info');

        $etat = $menu->disponible ? 'disponible' : 'indisponible';

        return back()->with('success', '"' . $menu->nom . '" est maintenant ' . $etat . '.');
    }
}
