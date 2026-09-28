<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Sécurité critique : Ne jamais exécuter ce seeder de test en environnement de production !
        if (app()->environment('production')) {
            $this->command?->warn('Exécution de DatabaseSeeder annulée : environnement de production détecté.');
            return;
        }

        User::updateOrCreate(
            ['email' => 'calebdassi@gmail.com'],
            [
                'name' => 'Caleb',
                'password' => Hash::make(env('SEED_DEFAULT_PASSWORD', 'password123')),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@vigilcore.internal'],
            [
                'name' => 'Admin VigilCore',
                'password' => Hash::make(env('SEED_DEFAULT_PASSWORD', 'password123')),
            ]
        );
    }
}
