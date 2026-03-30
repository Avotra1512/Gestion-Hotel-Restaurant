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
        Schema::create('commande_repas_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_repas_id')->constrained('commandes_repas')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->unsignedInteger('quantite')->default(1);
            $table->unsignedInteger('prix_unitaire'); // prix au moment de la commande
            $table->unsignedInteger('sous_total');    // quantite * prix_unitaire
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_repas_items');
    }
};
