<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chambre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ChambreController extends Controller
{
    /**
     * Liste toutes les chambres avec filtres optionnels.
     */
    public function index(Request $request)
    {
        $query = Chambre::query();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('type_chambre')) {
            $query->where('type_chambre', $request->type_chambre);
        }

        if ($request->filled('search')) {
            $query->where('numero_chambre', 'like', '%' . $request->search . '%');
        }

        $chambres = $query->orderBy('numero_chambre')->get();

        // Stats pour les cartes du haut
        $stats = [
            'total'         => Chambre::count(),
            'disponibles'   => Chambre::where('statut', 'disponible')->count(),
            'occupees'      => Chambre::where('statut', 'occupee')->count(),
            'hors_service'  => Chambre::where('statut', 'hors_service')->count(),
        ];

        return view('admin.chambres.index', compact('chambres', 'stats'));
    }

    /**
     * Formulaire de création d'une chambre.
     */
    public function create()
    {
        return view('admin.chambres.create');
    }

    /**
     * Enregistre une nouvelle chambre.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_chambre' => 'required|string|max:100|unique:chambres,numero_chambre',
            'type_chambre'   => ['required', Rule::in(['simple', 'double', 'triple'])],
            'prix_nuit'      => 'required|integer|min:0',
            'equipements'    => 'nullable|string',
            'statut'         => ['required', Rule::in(['disponible', 'occupee', 'hors_service'])],
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Conversion du champ equipements (texte séparé par virgules → tableau JSON)
        if (!empty($data['equipements'])) {
            $data['equipements'] = array_map('trim', explode(',', $data['equipements']));
        } else {
            $data['equipements'] = [];
        }

        // Upload de l'image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chambres', 'public');
            $data['image'] = $path;
        }

        $chambre = Chambre::create($data);

        NotificationService::tousClientsNouvelleChambre($chambre);
        NotificationService::gerantsChambreModifieeParAdmin($chambre);
        ActivityLog::log('chambre.creee', Auth::user()->name . ' a ajouté la chambre ' . $chambre->numero_chambre, 'chambres', '🛏️', 'success');

        return redirect()->route('admin.chambres.index')
            ->with('success', 'Chambre "' . $data['numero_chambre'] . '" ajoutée avec succès.');
    }

    /**
     * Formulaire d'édition d'une chambre.
     */
    public function edit(Chambre $chambre)
    {
        return view('admin.chambres.edit', compact('chambre'));
    }

    /**
     * Met à jour une chambre existante.
     */
    public function update(Request $request, Chambre $chambre)
    {
        $data = $request->validate([
            'numero_chambre' => ['required', 'string', 'max:100',
                Rule::unique('chambres', 'numero_chambre')->ignore($chambre->id)],
            'type_chambre'   => ['required', Rule::in(['simple', 'double', 'triple'])],
            'prix_nuit'      => 'required|integer|min:0',
            'equipements'    => 'nullable|string',
            'statut'         => ['required', Rule::in(['disponible', 'occupee', 'hors_service'])],
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Conversion équipements
        if (!empty($data['equipements'])) {
            $data['equipements'] = array_map('trim', explode(',', $data['equipements']));
        } else {
            $data['equipements'] = [];
        }

        // Upload de la nouvelle image (si fournie)
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($chambre->image) {
                Storage::disk('public')->delete($chambre->image);
            }
            $path = $request->file('image')->store('chambres', 'public');
            $data['image'] = $path;
        } else {
            // Garder l'image existante
            unset($data['image']);
        }

        $chambre->update($data);

        NotificationService::gerantsChambreModifieeParAdmin($chambre);
        ActivityLog::log('chambre.modifiee', Auth::user()->name . ' a modifié la chambre ' . $chambre->numero_chambre, 'chambres', '✏️', 'info');

        return redirect()->route('admin.chambres.index')
            ->with('success', 'Chambre "' . $chambre->numero_chambre . '" mise à jour avec succès.');
    }

    /**
     * Supprime une chambre.
     */
    public function destroy(Chambre $chambre)
    {
        $numero = $chambre->numero_chambre;

        // Supprimer l'image associée
        if ($chambre->image) {
            Storage::disk('public')->delete($chambre->image);
        }

        $chambre->delete();

        ActivityLog::log('chambre.supprimee', 'Chambre "' . $numero . '" supprimée', 'chambres', '🗑️', 'danger');


        return redirect()->route('admin.chambres.index')
            ->with('success', 'Chambre "' . $numero . '" supprimée avec succès.');
    }
}
