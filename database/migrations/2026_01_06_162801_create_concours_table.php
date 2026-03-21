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
        Schema::create('concours', function (Blueprint $table) {
            $table->id();
            $table->string('nom'); // nom du concours
            $table->string('description')->nullable();
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade'); // lien filière
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade'); // lien niveau
            $table->date('date_concours')->nullable(); // date du concours
            $table->date('date_limite_dossier')->nullable(); // date limite dépôt dossier
            $table->date('date_limite_paiement')->nullable(); // date limite paiement
            // $table->string('centre_depot')->nullable(); // centre de dépôt
            // $table->string('centre_examen')->nullable(); // centre d'examen
            $table->integer('taux_reussite')->nullable(); // pourcentage réussite
            $table->integer('taux_echec')->nullable(); // pourcentage échec
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concours');
    }
};
