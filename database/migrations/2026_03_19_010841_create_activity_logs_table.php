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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('role')->nullable();          // admin, gerant, client
            $table->string('action');                    // Ex: reservation.confirmee
            $table->string('description');               // Texte lisible
            $table->string('module')->nullable();        // reservations, commandes, chambres...
            $table->string('icone')->default('📋');
            $table->enum('niveau', ['info','success','warning','danger'])->default('info');
            $table->json('meta')->nullable();            // Données supplémentaires
            $table->string('ip')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index('module');
            $table->index('niveau');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
