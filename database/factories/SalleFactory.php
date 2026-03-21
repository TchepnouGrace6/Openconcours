<?php

namespace Database\Factories;

use App\Models\CentreExamen;
use App\Models\Salle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salle>
 */
class SalleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Salle::class;

    public function definition(): array
    {
        return [
            'centres_examen_id' => CentreExamen::factory(),                   // clé étrangère
            'nom_salle' => 'Salle '.$this->faker->numberBetween(1, 100), // string
            'capacite' => $this->faker->numberBetween(20, 60),        // integer
        ];
    }
}
