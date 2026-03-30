<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use App\Notifications\BienvenueClient;
use App\Services\NotificationService;

class RegisteredUserController extends Controller
{
    public function create() { return view('auth.register'); }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Création forcée en tant que client
        //$user = User::create([
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client', // Ici on garantit le rôle
        ]);

        NotificationService::clientBienvenue($user);
        NotificationService::gerantsNouveauClient($user);
        NotificationService::adminsNouveauClient($user);


        // Connexion automatique après inscription
        //Auth::login($user);

        // Redirection vers le dashboard client
        //return redirect()->route('client.dashboard');


        return redirect()->route('login')->with('success', 'Inscription réussie, vous pouvez vous connecter.');
    }
}
