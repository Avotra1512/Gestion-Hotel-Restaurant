<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReservationChambre;
use App\Models\CommandeRepas;
use App\Models\Chambre;
use App\Models\Menu;
use App\Notifications\NotifInApp;

class NotificationService
{
    // ══════════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ══════════════════════════════════════════════════════════════

    private static function notifierRole(string $role, string $icone, string $titre, string $message, string $lien = '#', string $niveau = 'info', string $module = 'general'): void
    {
        User::where('role', $role)->where('active', true)->get()
            ->each(fn($u) => $u->notify(new NotifInApp($icone, $titre, $message, $lien, $niveau, $module)));
    }

    private static function notifierUser(User $user, string $icone, string $titre, string $message, string $lien = '#', string $niveau = 'info', string $module = 'general'): void
    {
        $user->notify(new NotifInApp($icone, $titre, $message, $lien, $niveau, $module));
    }

    private static function refRes(int $id): string
    {
        return '#' . str_pad($id, 6, '0', STR_PAD_LEFT);
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ CLIENT — RÉSERVATIONS
    // ══════════════════════════════════════════════════════════════

    /** Client reçoit : sa réservation est en attente */
    public static function clientReservationEnAttente(ReservationChambre $r): void
    {
        if (!$r->user) return;
        self::notifierUser($r->user, '⏳', 'Réservation en attente',
            'Votre réservation ' . self::refRes($r->id) . ' pour la chambre ' . ($r->chambre?->numero_chambre ?? '—') . ' est en attente de validation.',
            '/client/reservations', 'info', 'reservations');
    }

    /** Client reçoit : réservation confirmée par le gérant */
    public static function clientReservationConfirmee(ReservationChambre $r): void
    {
        if (!$r->user) return;
        self::notifierUser($r->user, '✅', 'Réservation confirmée !',
            'Votre réservation ' . self::refRes($r->id) . ' a été confirmée. Chambre : ' . ($r->chambre?->numero_chambre ?? '—') . '.',
            '/client/reservations', 'success', 'reservations');
    }

    /** Client reçoit : réservation annulée/refusée par le gérant */
    public static function clientReservationRefusee(ReservationChambre $r): void
    {
        if (!$r->user) return;
        self::notifierUser($r->user, '❌', 'Réservation annulée',
            'Votre réservation ' . self::refRes($r->id) . ' a été annulée. Contactez-nous pour plus d\'informations.',
            '/client/reservations', 'danger', 'reservations');
    }

    /** Client reçoit : paiement validé */
    public static function clientPaiementValide(ReservationChambre $r): void
    {
        if (!$r->user) return;
        self::notifierUser($r->user, '💰', 'Paiement validé',
            'Le paiement de votre réservation ' . self::refRes($r->id) . ' — ' . number_format($r->prix_total, 0, ',', ' ') . ' Ar — a été validé. Facture disponible.',
            '/client/factures', 'success', 'reservations');
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ CLIENT — COMMANDES RESTAURANT
    // ══════════════════════════════════════════════════════════════

    /** Client reçoit : commande enregistrée */
    public static function clientCommandeEnregistree(CommandeRepas $c): void
    {
        if (!$c->user) return;
        self::notifierUser($c->user, '🍽️', 'Commande enregistrée',
            'Votre commande ' . self::refRes($c->id) . ' a été enregistrée et est en attente de confirmation.',
            '/client/commandes-repas', 'info', 'commandes');
    }

    /** Client reçoit : commande confirmée par le gérant */
    public static function clientCommandeConfirmee(CommandeRepas $c): void
    {
        if (!$c->user) return;
        self::notifierUser($c->user, '✅', 'Commande confirmée',
            'Votre commande ' . self::refRes($c->id) . ' a été confirmée par le restaurant.',
            '/client/commandes-repas', 'success', 'commandes');
    }

    /** Client reçoit : commande en préparation */
    public static function clientCommandeEnPreparation(CommandeRepas $c): void
    {
        if (!$c->user) return;
        self::notifierUser($c->user, '👨‍🍳', 'Commande en préparation',
            'Votre commande ' . self::refRes($c->id) . ' est en cours de préparation. Encore quelques minutes !',
            '/client/commandes-repas', 'info', 'commandes');
    }

    /** Client reçoit : commande prête */
    public static function clientCommandePrete(CommandeRepas $c): void
    {
        if (!$c->user) return;
        self::notifierUser($c->user, '🔔', 'Votre commande est prête !',
            'Votre commande ' . self::refRes($c->id) . ' est prête à être servie. Bon appétit !',
            '/client/commandes-repas', 'success', 'commandes');
    }

    /** Client reçoit : commande livrée */
    public static function clientCommandeLivree(CommandeRepas $c): void
    {
        if (!$c->user) return;
        self::notifierUser($c->user, '✅', 'Commande livrée',
            'Votre commande ' . self::refRes($c->id) . ' a été livrée. Facture disponible.',
            '/client/factures', 'success', 'commandes');
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ CLIENT — COMPTE & DIVERS
    // ══════════════════════════════════════════════════════════════

    /** Client reçoit : son profil a été modifié par l'admin */
    public static function clientProfilModifieParAdmin(User $client): void
    {
        self::notifierUser($client, '⚠️', 'Profil modifié par l\'admin',
            'Votre profil a été modifié par un administrateur. Si vous n\'en êtes pas à l\'origine, contactez-nous.',
            '/client/profil', 'warning', 'compte');
    }

    /** Client reçoit : son compte a été désactivé */
    public static function clientCompteDesactive(User $client): void
    {
        self::notifierUser($client, '🔐', 'Compte désactivé',
            'Votre compte a été désactivé par un administrateur. Contactez le support pour plus d\'informations.',
            '#', 'danger', 'compte');
    }

    /** Tous les clients reçoivent : nouvelle chambre ajoutée */
    public static function tousClientsNouvelleChambre(Chambre $chambre): void
    {
        self::notifierRole('client', '🛏️', 'Nouvelle chambre disponible !',
            'La chambre ' . $chambre->numero_chambre . ' (' . $chambre->type_chambre . ') vient d\'être ajoutée — ' . number_format($chambre->prix_nuit, 0, ',', ' ') . ' Ar/nuit.',
            '/client/chambres', 'info', 'chambres');
    }

    /** Tous les clients reçoivent : nouveau plat au menu */
    public static function tousClientsNouveauMenu(Menu $menu): void
    {
        self::notifierRole('client', '🍽️', 'Nouveau plat au menu !',
            '"' . $menu->nom . '" vient d\'être ajouté au menu restaurant — ' . number_format($menu->prix, 0, ',', ' ') . ' Ar.',
            '/client/restaurant', 'info', 'menus');
    }

    /** Tous les clients reçoivent : bienvenue (inscription) */
    public static function clientBienvenue(User $client): void
    {
        self::notifierUser($client, '🎉', 'Bienvenue chez MISALO !',
            'Votre compte a été créé avec succès. Réservez votre chambre ou commandez au restaurant dès maintenant !',
            '/client/dashboard', 'success', 'compte');
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ GÉRANT — RÉSERVATIONS
    // ══════════════════════════════════════════════════════════════

    /** Gérants reçoivent : nouvelle réservation à traiter */
    public static function gerantsNouvelleReservation(ReservationChambre $r): void
    {
        self::notifierRole('gerant', '📋', 'Nouvelle réservation à traiter',
            ($r->user?->name ?? $r->nom) . ' a réservé la chambre ' . ($r->chambre?->numero_chambre ?? '—') . ' — ' . number_format($r->prix_total, 0, ',', ' ') . ' Ar. À valider.',
            '/gerant/reservations/' . $r->id, 'warning', 'reservations');
    }

    /** Gérants reçoivent : réservation annulée par le client */
    public static function gerantsReservationAnnuleeParClient(ReservationChambre $r): void
    {
        self::notifierRole('gerant', '❌', 'Réservation annulée par le client',
            ($r->user?->name ?? $r->nom) . ' a annulé sa réservation ' . self::refRes($r->id) . ' — chambre ' . ($r->chambre?->numero_chambre ?? '—') . '.',
            '/gerant/reservations', 'danger', 'reservations');
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ GÉRANT — COMMANDES RESTAURANT
    // ══════════════════════════════════════════════════════════════

    /** Gérants reçoivent : nouvelle commande restaurant */
    public static function gerantsNouvelleCommande(CommandeRepas $c): void
    {
        self::notifierRole('gerant', '🍽️', 'Nouvelle commande restaurant',
            ($c->user?->name ?? $c->nom) . ' a commandé ' . $c->items->count() . ' plat(s) — ' . number_format($c->total, 0, ',', ' ') . ' Ar. À préparer.',
            '/gerant/commandes/' . $c->id, 'warning', 'commandes');
    }

    /** Gérants reçoivent : commande annulée par le client */
    public static function gerantsCommandeAnnuleeParClient(CommandeRepas $c): void
    {
        self::notifierRole('gerant', '❌', 'Commande annulée par le client',
            ($c->user?->name ?? $c->nom) . ' a annulé sa commande ' . self::refRes($c->id) . '.',
            '/gerant/commandes', 'danger', 'commandes');
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ GÉRANT — RAPPELS (appelés par le Scheduler)
    // ══════════════════════════════════════════════════════════════

    /** Gérants reçoivent : rappel réservations en attente depuis +24h */
    public static function gerantsRappelReservationsEnAttente(int $nb): void
    {
        self::notifierRole('gerant', '⚠️', 'Rappel : ' . $nb . ' réservation(s) en attente',
            $nb . ' réservation(s) sont en attente de validation depuis plus de 24h. Traitez-les dès que possible.',
            '/gerant/reservations?statut=en_attente', 'warning', 'reservations');
    }

    /** Gérants reçoivent : rappel commandes en attente depuis +1h */
    public static function gerantsRappelCommandesEnAttente(int $nb): void
    {
        self::notifierRole('gerant', '⚠️', 'Rappel : ' . $nb . ' commande(s) en attente',
            $nb . ' commande(s) de restaurant sont en attente depuis plus d\'1 heure. Veuillez les traiter.',
            '/gerant/commandes?statut=en_attente', 'warning', 'commandes');
    }

    /** Gérants reçoivent : réservations dépassées non traitées */
    public static function gerantsReservationsDepassees(int $nb): void
    {
        self::notifierRole('gerant', '🚨', 'Réservations dépassées non traitées',
            $nb . ' réservation(s) ont une date passée et n\'ont pas été traitées. Action requise.',
            '/gerant/reservations', 'danger', 'reservations');
    }

    /** Gérants reçoivent : chambre modifiée par l'admin */
    public static function gerantsChambreModifieeParAdmin(Chambre $chambre): void
    {
        self::notifierRole('gerant', '🛏️', 'Chambre modifiée par l\'admin',
            'La chambre ' . $chambre->numero_chambre . ' a été modifiée par un administrateur.',
            '/gerant/reservations', 'info', 'chambres');
    }

    /** Gérants reçoivent : menu modifié par l'admin */
    public static function gerantsMenuModifieParAdmin(Menu $menu): void
    {
        self::notifierRole('gerant', '🍽️', 'Menu modifié par l\'admin',
            'Le plat "' . $menu->nom . '" a été modifié par un administrateur.',
            '/gerant/menus', 'info', 'menus');
    }

    /** Gérants reçoivent : nouveau client inscrit */
    public static function gerantsNouveauClient(User $client): void
    {
        self::notifierRole('gerant', '👤', 'Nouveau client inscrit',
            $client->name . ' (' . $client->email . ') vient de créer un compte client.',
            '/gerant/clients', 'info', 'clients');
    }

    // ══════════════════════════════════════════════════════════════
    // ██████ ADMIN — SURVEILLANCE & SYSTÈME
    // ══════════════════════════════════════════════════════════════

    /** Admins reçoivent : nouveau client inscrit */
    public static function adminsNouveauClient(User $client): void
    {
        self::notifierRole('admin', '👤', 'Nouveau client inscrit',
            $client->name . ' (' . $client->email . ') vient de s\'inscrire sur la plateforme.',
            '/admin/users', 'info', 'clients');
    }

    /** Admins reçoivent : compte client supprimé */
    public static function adminsClientSupprime(string $nom, string $email): void
    {
        self::notifierRole('admin', '🗑️', 'Compte client supprimé',
            'Le compte de ' . $nom . ' (' . $email . ') a été supprimé.',
            '/admin/users', 'warning', 'clients');
    }

    /** Admins reçoivent : gérant a changé la dispo d'un menu */
    public static function adminsMenuToggleParGerant(Menu $menu, User $gerant): void
    {
        $etat = $menu->disponible ? 'activé' : 'désactivé';
        self::notifierRole('admin', '🔄', 'Menu ' . $etat . ' par le gérant',
            $gerant->name . ' a ' . $etat . ' le plat "' . $menu->nom . '".',
            '/admin/menus', 'info', 'menus');
    }

    /** Admins reçoivent : réservation payée (revenu encaissé) */
    public static function adminsReservationPayee(ReservationChambre $r): void
    {
        self::notifierRole('admin', '💰', 'Paiement chambre encaissé',
            'Réservation ' . self::refRes($r->id) . ' payée — ' . number_format($r->prix_total, 0, ',', ' ') . ' Ar encaissés.',
            '/admin/reservations', 'success', 'reservations');
    }

    /** Admins reçoivent : commande livrée (revenu restaurant) */
    public static function adminsCommandeLivree(CommandeRepas $c): void
    {
        self::notifierRole('admin', '💰', 'Revenu restaurant encaissé',
            'Commande ' . self::refRes($c->id) . ' livrée — ' . number_format($c->total, 0, ',', ' ') . ' Ar encaissés.',
            '/admin/reservations', 'success', 'commandes');
    }

    /** Admins reçoivent : trop d'annulations (alerte) */
    public static function adminsAlerteAnnulations(int $nb, string $type): void
    {
        self::notifierRole('admin', '🚨', 'Alerte : trop d\'annulations',
            $nb . ' ' . $type . ' ont été annulé(e)s cette semaine. Surveillance recommandée.',
            '/admin/statistiques', 'danger', 'surveillance');
    }

    /** Admins reçoivent : taux d'occupation faible */
    public static function adminsTauxOccupationFaible(int $taux): void
    {
        self::notifierRole('admin', '📉', 'Taux d\'occupation faible',
            'Taux d\'occupation actuel : ' . $taux . '%. Envisagez des actions pour attirer des réservations.',
            '/admin/statistiques', 'warning', 'surveillance');
    }

    /** Admins reçoivent : rapport quotidien des revenus (J-1) */
    public static function adminsRapportQuotidienRevenus(int $revenus_chambres, int $revenus_restaurant): void
    {
        $total = $revenus_chambres + $revenus_restaurant;
        self::notifierRole('admin', '📊', 'Rapport revenus d\'hier',
            'Chambres : ' . number_format($revenus_chambres, 0, ',', ' ') . ' Ar | Restaurant : ' . number_format($revenus_restaurant, 0, ',', ' ') . ' Ar | Total : ' . number_format($total, 0, ',', ' ') . ' Ar.',
            '/admin/statistiques', 'info', 'rapport');
    }

    /** Admins reçoivent : activité suspecte (trop de connexions échouées) */
    public static function adminsActiviteSuspecte(string $email, string $ip): void
    {
        self::notifierRole('admin', '🔴', 'Activité suspecte détectée',
            'Tentatives de connexion répétées échouées pour ' . $email . ' depuis l\'IP ' . $ip . '.',
            '/admin/logs', 'danger', 'securite');
    }
}
