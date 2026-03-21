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
        Schema::create('logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('utilisateur_id')->nullable()->constrained()->onDelete('set null'); // qui a fait l'action
        $table->string('action'); // ex : "Création de concours", "Paiement validé"
        $table->text('details')->nullable(); // infos supplémentaires
        $table->string('ip')->nullable(); // IP de l'utilisateur
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
