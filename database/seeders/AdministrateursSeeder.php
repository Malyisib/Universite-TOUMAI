<?php

namespace Database\Seeders;

use App\Models\Administrateur;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministrateursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administrators = [
            [
                'nom' => 'Administrateur',
                'prenom' => 'Youssouf',
                'telephone' => '+235 00 00 11 22',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'admin@toumai.com',
                'password' => 'admin123',
                'role' => 'super_admin',
            ],
            [
                'nom' => 'Recteur',
                'prenom' => 'Oumar',
                'telephone' => '+235 20 00 11 25',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'recteur@toumai.com',
                'password' => 'recteur123',
                'role' => 'director',
            ],
            [
                'nom' => 'Scolarité',
                'prenom' => 'Ali',
                'telephone' => '+235 20 00 11 26',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'scolarite@toumai.com',
                'password' => 'scolarite123',
                'role' => 'academic_coordinator',
            ],
            [
                'nom' => 'Gestionnaire',
                'prenom' => 'Issa',
                'telephone' => '+235 20 00 11 23',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'gestionnaire@toumai.com',
                'password' => 'gestionnaire123',
                'role' => 'secretary',
            ],
            [
                'nom' => 'Comptable',
                'prenom' => 'Brahim',
                'telephone' => '+235 20 00 11 24',
                'adresse' => 'Ndjaména, Tchad',
                'email' => 'comptable@toumai.com',
                'password' => 'comptable123',
                'role' => 'accountant',
            ]
        ];

        foreach ($administrators as $data) {
            // Skip if user already exists to make seeder idempotent
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                $this->command->info('Skipping existing user: ' . $data['email']);
                // Ensure role is assigned if missing
                if (!$existingUser->hasRole($data['role'])) {
                    $existingUser->assignRole($data['role']);
                    $this->command->info('Assigned missing role ' . $data['role'] . ' to ' . $data['email']);
                }
                continue;
            }

            // Create Administrator profile record
            $admin = Administrateur::create([
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
                'profile_type' => Administrateur::class,
                'profile_id' => $admin->id_administrateur,
            ]);

            // Assign role using Spatie
            $user->assignRole($data['role']);
        }
    }
}
