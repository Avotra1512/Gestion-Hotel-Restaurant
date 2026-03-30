<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Liste tous les plats avec filtres.
     * Le gérant peut voir tout mais ne peut que modifier la disponibilité.
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

        $stats = [
            'total'         => Menu::count(),
            'disponibles'   => Menu::where('disponible', true)->count(),
            'indisponibles' => Menu::where('disponible', false)->count(),
        ];

        return view('gerant.menus.index', compact('menus', 'stats'));
    }

    /**
     * Basculer disponibilité — SEULE action autorisée au gérant.
     */
    public function toggleDisponible(Menu $menu)
    {
        $menu->update(['disponible' => !$menu->disponible]);

        $etat    = $menu->disponible ? 'disponible' : 'indisponible';
        $message = $menu->disponible
            ? '✅ "' . $menu->nom . '" est maintenant disponible.'
            : '⏸ "' . $menu->nom . '" est maintenant indisponible.';

        return back()->with('success', $message);
    }
}