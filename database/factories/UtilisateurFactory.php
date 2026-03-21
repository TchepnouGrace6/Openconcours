<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    // Lien avec le modèle
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->name(),                 // string(255)
            'email' => $this->faker->unique()->safeEmail(),  // string(191) unique
            'password' => Hash::make('Password123!'),           // string hashé
            'role' => 'candidat',                           // enum par défaut
        ];
    }

    /**
     * Créer un utilisateur avec le rôle admin
     * Utilisation : Utilisateur::factory()->admin()->create()
     */
    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    /**
     * Créer un utilisateur avec le rôle candidat
     * Utilisation : Utilisateur::factory()->candidat()->create()
     */
    public function candidat(): static
    {
        return $this->state(['role' => 'candidat']);

    }
}
