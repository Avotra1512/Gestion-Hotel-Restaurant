<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    // Affiche la page de login
    public function create()
    {
        return view('auth.login');
    }

    // Gère la tentative de connexion
    public function store(Request $request)
    {
        // 1. Validation des champs
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Tentative de connexion
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 3. Redirection intelligente selon le rôle
            return match ($user->role) {
                'admin'  => redirect()->intended(route('admin.dashboard')),
                'gerant' => redirect()->intended(route('gerant.dashboard')),
                'client' => redirect()->intended(route('client.dashboard')),
                default  => redirect('/'),
            };
        }

        // 4. Si ça échoue, on renvoie une erreur sur l'email
        throw ValidationException::withMessages([
            'email' => __('Les identifiants fournis ne correspondent pas à nos enregistrements.'),
        ]);
    }

    // Gère la déconnexion
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}