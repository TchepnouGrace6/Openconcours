<?php

namespace Database\Factories;

use App\Models\ConcoursSession;
use App\Models\Paiement;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Paiement::class;

    public function definition(): array
    {
        static $counter = 1;

        return [
            'utilisateur_id' => Utilisateur::factory(),       // clé étrangère
            'concours_session_id' => ConcoursSession::factory(),   // clé étrangère
            'montant' => $this->faker->randomElement([5000, 10000, 15000, 25000]), // decimal(10,2)
            'moyen_paiement' => $this->faker->randomElement([ // string
                'mobile money',
                'carte bancaire',
                'espèces',
            ]),
            'numero_recu' => 'REC-'.date('Y').'-'.str_pad($counter++, 6, '0', STR_PAD_LEFT), // string unique
            'reference_transaction' => strtoupper(Str::random(10)), // string
            'statut' => 'paye',                      // statut par défaut
        ];
    }

    /**
     * Paiement en attente
     * Utilisation : Paiement::factory()->enAttente()->create()
     */
    public function enAttente(): static
    {
        return $this->state(['statut' => 'en_attente']);
    }

    /**
     * Paiement payé
     * Utilisation : Paiement::factory()->paye()->create()
     */
    public function paye(): static
    {
        return $this->state(['statut' => 'paye']);
    }
}
