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
        Schema::create('enrollements', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('concours_session_id')->constrained('concours_sessions')->onDelete('cascade');
            $table->foreignId('paiement_id')->constrained('paiements')->onDelete('cascade');
            $table->foreignId('centre_examen_id')->nullable()->constrained('centres_examen')->onDelete('set null');
            $table->foreignId('salle_id')->nullable()->constrained('salles')->onDelete('set null');

            // Numéros
            $table->string('numero_recu')->unique();
            $table->string('numero_enrollement')->unique();
            $table->integer('numero_table')->nullable();

            // Statut
            $table->enum('statut', ['en_attente', 'valide', 'refuse'])->default('en_attente');

            // Champs détaillés du candidat
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance');
            $table->enum('sexe', ['masculin', 'feminin']);
            $table->string('telephone');
            $table->string('adresse');
            $table->string('lieu_residence');
            $table->string('photo')->nullable();
            $table->string('nationalite');
            $table->string('numero_cni');
            $table->date('date_delivrance_cni');
            $table->string('region_origine');
            $table->string('nom_pere');
            $table->string('nom_mere');
            $table->string('telephone_pere');
            $table->string('telephone_mere');
            $table->string('nom_tuteur')->nullable();
            $table->string('telephone_tuteur')->nullable();
            $table->string('statut_matrimoniale');
            $table->string('niveau_etude');
            $table->string('serie_bac')->nullable();
            $table->string('mention_bac')->nullable();
            $table->year('annee_diplome')->nullable();
            $table->string('langue_parlee')->nullable();
            $table->boolean('est_handicape')->default(false);
            $table->string('type_handicap')->nullable();

            // Documents multiples
            $table->text('documents')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollements');
    }
};
