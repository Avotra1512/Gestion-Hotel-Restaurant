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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('categorie', [
                'entree',
                'plat_principal',
                'dessert',
                'boisson',
                'fast_food'
            ]);
            $table->text('description')->nullable();
            $table->unsignedInteger('prix'); // en Ariary
            $table->string('image')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();

            $table->index('categorie');
            $table->index('disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
