<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifie si l'admin existe déjà
        if (!Utilisateur::where('email', 'admin@gmail.com')->exists()) {
            Utilisateur::create([
                'nom' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'), // 🔹 mot de passe sécurisé
                'role' => 'admin',
            ]);

            $this->command->info('Administrateur créé : admin@gmail.com / admin123');
        } else {
            $this->command->info('L’administrateur existe déjà.');
        }
    
    }
}
