<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enseignant;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EnseignantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enseignants = [
            [
                'nom' => 'Hissein',
                'prenom' => 'Cherif',
                'telephone' => '+235 00 11 22 33',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'cherif@toumai.com',
                'password' => 'enseignant123',
            ],
            [
                'nom' => ' ',
                'prenom' => 'Assadick',
                'telephone' => '+235 00 11 22 30',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'asadick@toumai.com',
                'password' => 'enseignant123',
            ],
     
            ];

        foreach ($enseignants as $data) {
            // Skip if user already exists to make seeder idempotent
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                $this->command->info('Skipping existing user: ' . $data['email']);
                if (!$existingUser->hasRole('teacher')) {
                    $existingUser->assignRole('teacher');
                    $this->command->info('Assigned missing role teacher to ' . $data['email']);
                }
                continue;
            }

            // Create Enseignant profile record
            $enseignant = Enseignant::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'telephone' => $data['telephone'],
                'adresse' => $data['adresse'],
            ]);

            // Create User account with polymorphic relationship
            $user = User::create([
                'name' => trim($data['prenom'] . ' ' . $data['nom']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
                'email_verified_at' => now(),
                'profile_type' => Enseignant::class,
                'profile_id' => $enseignant->id_enseignant,
            ]);

            // Assign teacher role using Spatie
            $user->assignRole('teacher');
        }
    }
}