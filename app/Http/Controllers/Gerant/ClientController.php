<?php

namespace App\Http\Controllers\Gerant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReservationChambre;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'client')->withCount('reservationChambres');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }

        $clients = $query->latest()->paginate(20)->withQueryString();
        return view('gerant.clients.index', compact('clients'));
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'client', 404);

        $reservations = ReservationChambre::with('chambre')
            ->where('user_id', $user->id)->latest()->get();

        $stats = [
            'total'      => $reservations->count(),
            'en_attente' => $reservations->where('statut', 'en_attente')->count(),
            'confirmees' => $reservations->where('statut', 'confirmee')->count(),
            'payees'     => $reservations->where('statut', 'payee')->count(),
            'revenus'    => $reservations->where('statut', 'payee')->sum('prix_total'),
        ];

        return view('gerant.clients.show', compact('user', 'reservations', 'stats'));
    }
}