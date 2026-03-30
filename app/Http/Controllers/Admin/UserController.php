<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Liste tous les utilisateurs avec filtres.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active === '1');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
            );
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'admins'   => User::where('role', 'admin')->count(),
            'gerants'  => User::where('role', 'gerant')->count(),
            'clients'  => User::where('role', 'client')->count(),
            'actifs'   => User::where('active', true)->count(),
            'inactifs' => User::where('active', false)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Formulaire de création d'un utilisateur.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => ['required', Rule::in(['admin', 'gerant', 'client'])],
            'password' => 'required|string|min:8|confirmed',
            'active'   => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['active']   = $request->has('active');

        $user = User::create($data);

        if ($data['role'] === 'client') {
            NotificationService::clientBienvenue($user);
            NotificationService::gerantsNouveauClient($user);
            NotificationService::adminsNouveauClient($user);
        }
        ActivityLog::log('user.cree', Auth::user()->name . ' a créé le compte ' . $user->name . ' (' . $user->role . ')', 'users', '👤', 'success');

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "' . $data['name'] . '" créé avec succès.');
    }

    /**
     * Formulaire d'édition d'un utilisateur.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'gerant', 'client'])],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data['active'] = $request->has('active');

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Empêcher l'admin de se désactiver lui-même
        if ($user->id === auth()->id() && !$data['active']) {
            return back()->withErrors(['active' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update($data);

        if ($user->role === 'client') {
            NotificationService::clientProfilModifieParAdmin($user);
        }
        ActivityLog::log('user.modifie', Auth::user()->name . ' a modifié le compte de ' . $user->name, 'users', '✏️', 'info');

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "' . $user->name . '" mis à jour.');
    }

    /**
     * Activer / désactiver un utilisateur (toggle rapide).
     */
    public function toggleActive(User $user)
    {
        // Empêcher l'admin de se désactiver lui-même
        if ($user->id === auth()->id()) {
            return back()->withErrors(['active' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update(['active' => !$user->active]);

        if (!$user->active) {
            NotificationService::clientCompteDesactive($user);
        }
        ActivityLog::log('user.toggle', Auth::user()->name . ' a ' . ($user->active ? 'activé' : 'désactivé') . ' le compte de ' . $user->name, 'users', '🔐', $user->active ? 'success' : 'warning');

        $etat    = $user->active ? 'activé' : 'désactivé';
        $message = '"' . $user->name . '" a été ' . $etat . '.';

        return back()->with('success', $message);
    }

    /**
     * Modifier uniquement le rôle.
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(['admin', 'gerant', 'client'])],
        ]);

        // Empêcher l'admin de changer son propre rôle
        if ($user->id === auth()->id()) {
            return back()->withErrors(['role' => 'Vous ne pouvez pas modifier votre propre rôle.']);
        }

        $user->update(['role' => $request->role]);


        ActivityLog::log('user.role', 'Rôle de "' . $user->name . '" changé en ' . $request->role, 'users', '🎭', 'warning');

        return back()->with('success', 'Rôle de "' . $user->name . '" changé en ' . $request->role . '.');
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        // Empêcher l'admin de se supprimer lui-même
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        $nom = $user->name;
        $user->delete();

        if (!$user->active) {
            NotificationService::clientCompteDesactive($user);
        }
        ActivityLog::log('user.toggle', Auth::user()->name . ' a ' . ($user->active ? 'activé' : 'désactivé') . ' le compte de ' . $user->name, 'users', '🔐', $user->active ? 'success' : 'warning');

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur "' . $nom . '" supprimé.');
    }
}
