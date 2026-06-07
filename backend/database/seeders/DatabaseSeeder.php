<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Compte administrateur de référence (gestion complète : CRUD events/salles/tickets)
        User::firstOrCreate(
            ['email' => 'admin@evenements.test'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Compte utilisateur de test (réservations uniquement)
        User::firstOrCreate(
            ['email' => 'user@evenements.test'],
            [
                'name' => 'Utilisateur Test',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );
    }
}
