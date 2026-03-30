<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chambres', function (Blueprint $table) {
            $table->id();
            $table->string('numero_chambre')->unique();
            $table->enum('type_chambre', ['simple', 'double', 'triple']);
            $table->unsignedInteger('prix_nuit'); // en Ariary
            $table->json('equipements')->nullable();
            $table->enum('statut', ['disponible', 'occupee', 'hors_service'])->default('disponible');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->index('statut');
            $table->index('type_chambre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chambres');
    }
};
