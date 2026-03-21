<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CentreExamen;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentreExamen>
 */
class CentreExamenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
       protected $model = CentreExamen::class;
 
    public function definition(): array
    {
        return [
            'nom'      => 'Centre ' . $this->faker->unique()->city(), // string
            'adresse'  => $this->faker->streetAddress(),              // string
            'capacite' => $this->faker->numberBetween(50, 500),       // integer
        ];
    }
}
