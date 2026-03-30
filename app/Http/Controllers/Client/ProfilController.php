<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user(); // ← Le cast via PHPDoc résout l'erreur IDE

        $reservations = ReservationChambre::with('chambre')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $statsReservations = [
            'total'      => $reservations->count(),
            'en_attente' => $reservations->where('statut', 'en_attente')->count(),
            'confirmee'  => $reservations->where('statut', 'confirmee')->count(),
            'payee'      => $reservations->where('statut', 'payee')->count(),
            'terminee'   => $reservations->where('statut', 'terminee')->count(),
            'annulee'    => $reservations->where('statut', 'annulee')->count(),
            'depenses'   => $reservations->whereIn('statut', ['payee','terminee'])->sum('prix_total'),
        ];

        $commandes = CommandeRepas::with('items.menu')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $statsCommandes = [
            'total'    => $commandes->count(),
            'livrees'  => $commandes->where('statut', 'livree')->count(),
            'annulees' => $commandes->where('statut', 'annulee')->count(),
            'depenses' => $commandes->where('statut', 'livree')->sum('total'),
        ];

        $totalDepense        = $statsReservations['depenses'] + $statsCommandes['depenses'];
        $derniereReservation = $reservations->first();
        $derniereCommande    = $commandes->first();

        $chambreFavorite = $reservations
            ->whereNotNull('chambre_id')
            ->groupBy('chambre_id')
            ->map(fn($g) => ['chambre' => $g->first()->chambre, 'count' => $g->count()])
            ->sortByDesc('count')
            ->first();

        return view('client.profil.index', compact(
            'user', 'reservations', 'statsReservations',
            'commandes', 'statsCommandes', 'totalDepense',
            'derniereReservation', 'derniereCommande', 'chambreFavorite'
        ));
    }

    public function updateInfos(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success_infos', 'Vos informations ont été mises à jour.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                         ->with('tab', 'securite');
        }

        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Le nouveau mot de passe doit être différent de l\'ancien.'])
                         ->with('tab', 'securite');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success_password', 'Mot de passe modifié avec succès.')
                     ->with('tab', 'securite');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_delete' => 'required|in:SUPPRIMER',
        ]);

        /** @var User $user */
        $user = Auth::user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Votre compte a été supprimé avec succès.');
    }
}
