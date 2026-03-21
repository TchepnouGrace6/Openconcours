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
        Schema::create('concours_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concours_id')->constrained('concours')->onDelete('cascade'); // lien avec le concours
            $table->string('nom_session'); // ex: "Session principale", "Rattrapage"
            $table->date('date_session'); // date de la session
            $table->foreignId('centres_examen_id')->constrained('centres_examen')->onDelete('cascade');
            // $table->string('centres_examen'); // centre d'examen
            // $table->string('salle')->nullable(); // salle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concours_sessions');
    }
};
