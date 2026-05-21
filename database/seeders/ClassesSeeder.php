<?php

namespace Database\Seeders;

use App\Models\Classe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            // Maternelle (Preschool) - Ages 3-5
            ['nom_classe' => 'Licence L1', 'niveau' => 1],
            ['nom_classe' => 'Licence L2', 'niveau' => 2],
            ['nom_classe' => 'Licence L3', 'niveau' => 3],
        ];
        foreach ($classes as $classe) {
            Classe::create($classe);
        }
        
        $this->command->info('✅ Created ' . count($classes) . ' classes');
    }
}
