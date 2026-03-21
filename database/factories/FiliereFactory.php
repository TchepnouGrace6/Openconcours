<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Filiere;
use App\Models\Departement;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filiere>
 */
class FiliereFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
        protected $model = Filiere::class;
 
    public function definition(): array
    {
        return [
            'nom'            => $this->faker->unique()->words(2, true), // string unique
            'description'    => $this->faker->sentence(15),             // string(255)
            'departement_id' => Departement::factory(),                 // clé étrangère
        ];
    }
}
