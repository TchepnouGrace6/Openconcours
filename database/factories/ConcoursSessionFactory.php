<?php

namespace Database\Factories;

use App\Models\CentreExamen;
use App\Models\Concours;
use App\Models\ConcoursSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConcoursSession>
 */
class ConcoursSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = ConcoursSession::class;

    public function definition(): array
    {
        return [
            'concours_id' => Concours::factory(),                              // clé étrangère
            'nom_session' => $this->faker->randomElement([                     // string
                'Session principale',
                'Session de rattrapage',
                'Session spéciale',
            ]),
            'date_session' => now()->addMonths(3)->toDateString(),              // date
            'centres_examen_id' => CentreExamen::factory(),                          // clé étrangère
        ];
    }
}
