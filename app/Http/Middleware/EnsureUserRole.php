<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. 🔒 Vérifier si le compte est ACTIF
        if (!$user->active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
            ]);
        }

        // 3. Vérifier le rôle
        if ($user->role !== $role) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'email' => 'Accès non autorisé.'
            ]);
        }

        // 4. Tout est OK
        return $next($request);
    }
}