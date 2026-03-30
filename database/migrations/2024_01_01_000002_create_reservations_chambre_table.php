<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations_chambre', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->foreignId('chambre_id')->constrained('chambres')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Dates (deux modes : nuit unique OU séjour multi-nuits)
            $table->date('date_reservation')->nullable(); // nuit unique
            $table->date('date_arrivee')->nullable();     // séjour multi-nuits
            $table->date('date_depart')->nullable();      // séjour multi-nuits

            // Prix calculé
            $table->unsignedInteger('prix_total')->nullable();

            // Infos client
            $table->string('nom');
            $table->string('email');
            $table->string('motif')->nullable();

            // Statut — pas de paiement en ligne, flux physique
            // en_attente → confirmee → payee → terminee
            $table->enum('statut', ['en_attente', 'confirmee', 'payee', 'terminee', 'annulee'])
                  ->default('en_attente');

            $table->timestamps();

            $table->index('statut');
            $table->index('chambre_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations_chambre');
    }
};
