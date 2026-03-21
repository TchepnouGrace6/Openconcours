<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Enrollement;
use App\Models\Utilisateur;
use App\Models\ConcoursSession;
use App\Models\Paiement;
use App\Models\CentreExamen;
use App\Models\CentreDepot;
use App\Models\Salle;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollement>
 */
class EnrollementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
        protected $model = Enrollement::class;
 
    public function definition(): array
    {
        return [
            // Relations
            'utilisateur_id'      => Utilisateur::factory(),
            'concours_session_id' => ConcoursSession::factory(),
            'paiement_id'         => Paiement::factory(),
            'centre_examen_id'    => CentreExamen::factory(),   // nullable
            'salle_id'            => Salle::factory(),           // nullable
 
            // Numéros
            'numero_recu'         => 'REC-' . $this->faker->unique()->numerify('######'),
            'numero_enrollement'  => 'ENR-' . date('Y') . '-' . strtoupper($this->faker->unique()->lexify('????????')),
            'numero_table'        => $this->faker->numberBetween(1, 100), // integer nullable
 
            // Statut
            'statut'              => 'en_attente', // enum : en_attente | valide | refuse
 
            // Informations personnelles
            'nom'                 => $this->faker->lastName(),
            'prenom'              => $this->faker->firstName(),
            'date_naissance'      => $this->faker->date('Y-m-d', '-18 years'),
            'sexe'                => $this->faker->randomElement(['masculin', 'feminin']),
            'telephone'           => $this->faker->numerify('6########'),
            'adresse'             => $this->faker->streetAddress(),
            'lieu_residence'      => $this->faker->city(),
            'photo'               => null,                        // string nullable
            'nationalite'         => 'Camerounaise',
            'numero_cni'          => $this->faker->numerify('#########'),
            'date_delivrance_cni' => $this->faker->date('Y-m-d', '-2 years'),
            'region_origine'      => $this->faker->randomElement(['Centre', 'Littoral', 'Nord', 'Sud', 'Ouest', 'Est']),
 
            // Informations famille
            'nom_pere'            => $this->faker->name('male'),
            'nom_mere'            => $this->faker->name('female'),
            'telephone_pere'      => $this->faker->numerify('6########'),
            'telephone_mere'      => $this->faker->numerify('6########'),
            'nom_tuteur'          => null,        // string nullable
            'telephone_tuteur'    => null,        // string nullable
 
            // Informations académiques
            'statut_matrimoniale' => $this->faker->randomElement(['celibataire', 'marie', 'divorce']),
            'niveau_etude'        => $this->faker->randomElement(['Licence', 'Master', 'BTS', 'Doctorat']),
            'serie_bac'           => $this->faker->randomElement(['A', 'C', 'D', 'F', null]), // nullable
            'mention_bac'         => $this->faker->randomElement(['Passable', 'Assez bien', 'Bien', null]), // nullable
            'annee_diplome'       => $this->faker->year(),       // year nullable
            'langue_parlee'       => 'Français',                 // string nullable
 
            // Handicap
            'est_handicape'       => false,                      // boolean
            'type_handicap'       => null,                       // string nullable
 
            // Documents
            'documents'           => json_encode([]),            // text nullable
        ];
    }
 
    /**
     * Enrôlement validé
     * Utilisation : Enrollement::factory()->valide()->create()
     */
    public function valide(): static
    {
        return $this->state(['statut' => 'valide']);
    }
 
    /**
     * Enrôlement refusé
     * Utilisation : Enrollement::factory()->refuse()->create()
     */
    public function refuse(): static
    {
        return $this->state(['statut' => 'refuse']);
    }
 
    /**
     * Candidat handicapé
     * Utilisation : Enrollement::factory()->handicape()->create()
     */
    public function handicape(): static
    {
        return $this->state([
            'est_handicape' => true,
            'type_handicap' => $this->faker->randomElement(['Visuel', 'Auditif', 'Moteur']),
        ]);
    }
}
