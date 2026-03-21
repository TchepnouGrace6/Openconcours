<?php

namespace Database\Factories;

use App\Models\Concours;
use App\Models\Filiere;
use App\Models\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Concours>
 */
class ConcoursFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Concours::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->words(3, true),              // string
            'description' => $this->faker->sentence(),                  // string nullable
            'filiere_id' => Filiere::factory(),                        // clé étrangère
            'niveau_id' => Niveau::factory(),                         // clé étrangère
            'date_concours' => now()->addMonths(3)->toDateString(),       // date nullable
            'date_limite_dossier' => now()->addMonth()->toDateString(),         // date nullable
            'date_limite_paiement' => now()->addMonths(2)->toDateString(),       // date nullable
            'taux_reussite' => $this->faker->numberBetween(10, 80),       // integer nullable
            'taux_echec' => $this->faker->numberBetween(10, 80),       // integer nullable
        ];
    }
}
