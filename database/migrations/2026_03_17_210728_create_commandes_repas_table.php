<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes_repas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Infos client
            $table->string('nom');
            $table->string('email');

            // Statut de la commande
            $table->enum('statut', [
                'en_attente',
                'en_preparation',
                'prete',
                'livree',
                'annulee'
            ])->default('en_attente');

            // Montant total
            $table->unsignedInteger('total');

            // Note optionnelle du client
            $table->text('note')->nullable();

            $table->timestamps();

            $table->index('statut');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes_repas');
    }
};
