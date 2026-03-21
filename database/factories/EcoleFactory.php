<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ecole;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ecole>
 */
class EcoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = Ecole::class;

    public function definition(): array
    {
        return [
            'nom'     => $this->faker->unique()->company(),   // string unique
            'adresse' => $this->faker->streetAddress(),       // string nullable
            'ville'   => $this->faker->city(),                // string nullable
            'pays'    => $this->faker->country(),             // string nullable
        ];
    }
}
