<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matiere;

class MatieresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matieres = [
            [
                'nom_matiere' => 'Architecture des Ordinateurs',
                'code_matiere' => 'MATH',
                'description' => 'Architecture des Ordinateurs',
                'coefficient' => 4,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Technologie Web',
                'code_matiere' => 'FR',
                'description' => 'Technologie Web',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Anglais',
                'code_matiere' => 'ANG',
                'description' => 'Langue anglaise',
                'coefficient' => 2,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Base de données',
                'code_matiere' => 'HG',
                'description' => 'Base de données',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Télécommunication',
                'code_matiere' => 'PHY',
                'description' => 'Télécommunication',
                'coefficient' => 3,
                'active' => true,
            ],
            [
                'nom_matiere' => 'Administration Système et Réseaux',
                'code_matiere' => 'SVT',
                'description' => 'Administration Système et Réseaux',
                'coefficient' => 3,
                'active' => true,
            ],
        ];

        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(
                ['code_matiere' => $matiere['code_matiere']],
                $matiere
            );
        }
    }
}
