<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {

        User::firstOrCreate(
            ['email' => 'admin@evenements.test'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

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
