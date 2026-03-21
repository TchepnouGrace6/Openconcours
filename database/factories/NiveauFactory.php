<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Niveau;
use App\Models\Filiere;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Niveau>
 */
class NiveauFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
       protected $model = Niveau::class;
 
    public function definition(): array
    {
        return [
            'nom'        => $this->faker->randomElement(['L1', 'L2', 'L3', 'M1', 'M2', 'BTS1', 'BTS2']), // string
            'filiere_id' => Filiere::factory(),  // clé étrangère
        ];
    }
}
