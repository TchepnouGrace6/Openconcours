<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CentreDepot;
use App\Models\Concours;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CentreDepot>
 */
class CentreDepotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
       protected $model = CentreDepot::class;
 
    public function definition(): array
    {
        return [
            'concours_id' => Concours::factory(),                        // clé étrangère
            'nom'         => 'Dépôt ' . $this->faker->unique()->city(),  // string
            'adresse'     => $this->faker->streetAddress(),              // string nullable
        ];
    }
}
