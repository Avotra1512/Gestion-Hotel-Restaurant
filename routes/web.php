<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ChambreController;
use App\Http\Controllers\Client\ReservationChambreController;
use App\Http\Controllers\Gerant\DashboardController as GerantDashboardController;
use App\Http\Controllers\Gerant\ReservationController as GerantReservationController;
use App\Http\Controllers\Gerant\ClientController as GerantClientController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Client\CommandeRepasController;
use App\Http\Controllers\Gerant\CommandeRepasController as GerantCommandeController;
use App\Http\Controllers\Gerant\MenuController as GerantMenuController;
use App\Http\Controllers\Gerant\StatistiqueController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminStatiqueController;
use App\Http\Controllers\Client\FactureController;
use App\Http\Controllers\Client\ProfilController;
use App\Http\Controllers\Admin\ReservationViewController;
use App\Http\Controllers\Client\FideliteController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ActivityLogController;



// PAGES PUBLIQUES
Route::get('/', fn() => view('home'))->name('home');
Route::get('/hotel', fn() => view('hotel'))->name('hotel');
Route::get('/restaurant', fn() => view('restaurant'))->name('restaurant');
Route::get('/contact', fn() => view('contact'))->name('contact');

// AUTHENTIFICATION
Route::get('/login',    [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login',   [AuthenticatedSessionController::class, 'store']);
Route::post('/logout',  [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register',[RegisteredUserController::class, 'store']);
Route::get('/forgot-password',         [PasswordResetController::class, 'showForgot'])->name('password.request');
Route::post('/forgot-password',        [PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/reset-password/{token}',  [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password',         [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ROUTES PROTÉGÉES
Route::middleware(['auth', 'no-cache'])->group(function () {

    Route::get('/client/dashboard', [DashboardController::class, 'client'])
        ->middleware('role:client')->name('client.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin')->name('admin.dashboard');

    // ── ADMIN ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('chambres', ChambreController::class);
         // Ce Route::resource génère automatiquement :
        // GET    /admin/chambres             → index    → admin.chambres.index
        // GET    /admin/chambres/create      → create   → admin.chambres.create
        // POST   /admin/chambres             → store    → admin.chambres.store
        // GET    /admin/chambres/{chambre}/edit   → edit → admin.chambres.edit
        // PUT    /admin/chambres/{chambre}   → update   → admin.chambres.update
        // DELETE /admin/chambres/{chambre}  → destroy  → admin.chambres.destroy


        Route::resource('menus', MenuController::class);
        Route::patch('menus/{menu}/toggle-disponible', [MenuController::class, 'toggleDisponible'])
                ->name('menus.toggle-disponible');

        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::patch('users/{user}/role',          [UserController::class, 'updateRole'])->name('users.role');

        Route::get('/statistiques', [AdminStatiqueController::class, 'index'])
            ->name('statistiques.index');

        Route::get('/reservations',         [ReservationViewController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}', [ReservationViewController::class, 'show'])->name('reservations.show');

        // Exports CSV
        Route::get('/export/reservations',  [ReservationViewController::class, 'exportReservationsCsv'])->name('export.reservations');
        Route::get('/export/clients',       [ReservationViewController::class, 'exportClientsCsv'])     ->name('export.clients');
        Route::get('/export/commandes',     [ReservationViewController::class, 'exportCommandesCsv'])   ->name('export.commandes');

        Route::get('/export', fn() => view('admin.export.index'))->name('export.index');

        Route::get('/logs',          [ActivityLogController::class, 'index'])->name('logs.index');
        Route::delete('/logs/purge', [ActivityLogController::class, 'purge'])->name('logs.purge');
    });

    // ── CLIENT ─────────────────────────────────────────────────────
    Route::middleware('role:client')->prefix('client')->name('client.')->group(function () {
        Route::get('/chambres', [ReservationChambreController::class, 'index'])->name('chambres.index');
        Route::get('/chambres/{chambre}/reserver', [ReservationChambreController::class, 'create'])->name('chambres.reserver');
        Route::post('/reservations', [ReservationChambreController::class, 'store'])->name('reservations.store');
        Route::get('/reservations/{reservation}/confirmation', [ReservationChambreController::class, 'confirmation'])->name('reservations.confirmation');
        Route::get('/reservations', [ReservationChambreController::class, 'mesReservations'])->name('reservations.index');
        Route::patch('/reservations/{reservation}/annuler', [ReservationChambreController::class, 'annuler'])->name('reservations.annuler');


        Route::get('/restaurant',                    [CommandeRepasController::class, 'index'])           ->name('restaurant.index');
        Route::post('/restaurant/panier/ajouter',    [CommandeRepasController::class, 'ajouterAuPanier']) ->name('restaurant.panier.ajouter');
        Route::patch('/restaurant/panier/modifier',  [CommandeRepasController::class, 'modifierQuantite'])->name('restaurant.panier.modifier');
        Route::delete('/restaurant/panier/supprimer',[CommandeRepasController::class, 'supprimerDuPanier'])->name('restaurant.panier.supprimer');
        Route::delete('/restaurant/panier/vider',    [CommandeRepasController::class, 'viderPanier'])     ->name('restaurant.panier.vider');
        Route::get('/restaurant/recapitulatif',      [CommandeRepasController::class, 'recapitulatif'])   ->name('restaurant.recapitulatif');
        Route::post('/restaurant/confirmer',         [CommandeRepasController::class, 'confirmer'])       ->name('restaurant.confirmer');
        Route::get('/restaurant/confirmation/{commande}', [CommandeRepasController::class, 'confirmation'])->name('restaurant.confirmation');
        Route::get('/commandes-repas',               [CommandeRepasController::class, 'mesCommandes'])    ->name('restaurant.commandes');
        Route::patch('/commandes-repas/{commande}/annuler', [CommandeRepasController::class, 'annuler'])  ->name('restaurant.annuler');
        Route::get('/commandes-repas/{commande}/facture',   [CommandeRepasController::class, 'facturePdf'])->name('restaurant.facture');



        Route::get('/factures',                                    [FactureController::class, 'index'])             ->name('factures.index');
        Route::get('/factures/reservation/{reservation}/pdf',      [FactureController::class, 'factureReservation'])->name('factures.reservation');
        Route::get('/factures/commande/{commande}/pdf',            [FactureController::class, 'factureCommande'])   ->name('factures.commande');
        Route::get('/factures/groupee/pdf',                        [FactureController::class, 'factureGroupee'])    ->name('factures.groupee');



        Route::get('/profil',                    [ProfilController::class, 'index'])          ->name('profil.index');
        Route::patch('/profil/infos',            [ProfilController::class, 'updateInfos'])    ->name('profil.infos');
        Route::patch('/profil/password',         [ProfilController::class, 'updatePassword']) ->name('profil.password');
        Route::delete('/profil/delete-account',  [ProfilController::class, 'deleteAccount'])  ->name('profil.delete');

        Route::get('/fidelite', [FideliteController::class, 'index'])->name('fidelite.index');
    });

    // ── GÉRANT ─────────────────────────────────────────────────────
    Route::middleware('role:gerant')->prefix('gerant')->name('gerant.')->group(function () {
        Route::get('/dashboard', [GerantDashboardController::class, 'index'])->name('dashboard');

        Route::get('/reservations', [GerantReservationController::class, 'index'])->name('reservations.index');
        Route::get('/reservations/{reservation}', [GerantReservationController::class, 'show'])->name('reservations.show');
        Route::patch('/reservations/{reservation}/statut', [GerantReservationController::class, 'updateStatut'])->name('reservations.statut');
        Route::patch('/reservations/{reservation}/valider-paiement', [GerantReservationController::class, 'validerPaiement'])->name('reservations.valider-paiement');
        Route::get('/reservations/{reservation}/facture', [GerantReservationController::class, 'facturePdf'])->name('reservations.facture');
        Route::post('/reservations/mise-a-jour-auto', [GerantReservationController::class, 'mettreAJourAuto'])->name('reservations.auto-update');

        Route::get('/clients', [GerantClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{user}', [GerantClientController::class, 'show'])->name('clients.show');



        Route::get('/commandes',                          [GerantCommandeController::class, 'index'])              ->name('commandes.index');
        Route::get('/commandes/{commande}',               [GerantCommandeController::class, 'show'])               ->name('commandes.show');
        Route::patch('/commandes/{commande}/statut',      [GerantCommandeController::class, 'updateStatut'])       ->name('commandes.statut');
        Route::patch('/commandes/{commande}/preparation', [GerantCommandeController::class, 'passerEnPreparation'])->name('commandes.preparation');
        Route::patch('/commandes/{commande}/prete',       [GerantCommandeController::class, 'marquerPrete'])       ->name('commandes.prete');
        Route::patch('/commandes/{commande}/livree',      [GerantCommandeController::class, 'marquerLivree'])      ->name('commandes.livree');
        Route::get('/commandes/{commande}/facture',       [GerantCommandeController::class, 'facturePdf'])         ->name('commandes.facture');



        Route::get('/menus', [GerantMenuController::class, 'index'])
            ->name('menus.index');

        Route::patch('/menus/{menu}/toggle-disponible', [GerantMenuController::class, 'toggleDisponible'])
            ->name('menus.toggle-disponible');



        Route::get('/statistiques/ventes',   [StatistiqueController::class, 'ventes'])          ->name('statistiques.ventes');
        Route::get('/statistiques/planning', [StatistiqueController::class, 'planningChambres'])->name('statistiques.planning');
        Route::get('/statistiques/rapport',  [StatistiqueController::class, 'rapportQuotidien'])->name('statistiques.rapport');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/notifications',              [NotificationController::class, 'index'])        ->name('notifications.index');
        Route::patch('/notifications/{id}/read',  [NotificationController::class, 'markAsRead'])   ->name('notifications.read');
        Route::patch('/notifications/read-all',   [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
});

