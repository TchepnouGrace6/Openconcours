<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Ecole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Departement>
 */
class DepartementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Departement::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->words(2, true), // string unique
            'description' => $this->faker->sentence(10),             // string(200) nullable
            'ecole_id' => Ecole::factory(),                       // clé étrangère
        ];
    }
}
