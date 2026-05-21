<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Enseignant;
use App\Models\Matiere;
use App\Models\Classe;

class EnseignantMatiereClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get teachers, subjects, and classes
        $teachers = Enseignant::all();
        $matieres = Matiere::all();
        $classes = Classe::all();

        if ($teachers->isEmpty() || $matieres->isEmpty() || $classes->isEmpty()) {
            $this->command->warn('Missing data: Teachers, Matieres, or Classes. Make sure to seed them first.');
            return;
        }

        // Sample assignments - assign ALL teachers to subjects
        $assignments = [
            // Sidi Mohamed El Moctar - Math and Physics
            [
                'teacher_nom' => 'Hissein',
                'teacher_prenom' => 'Cherif',
                'subjects' => ['MATH', 'PHY'],
                'classes' => ['Licence L3', 'Licence L1', ]
            ],
            // Aminetou Mint Mohamedou - French and English
            [
                'teacher_nom' => ' ',
                'teacher_prenom' => 'Assadick',
                'subjects' => ['FR', 'ANG'],
                'classes' => ['Licence L2']
            ],
        ];

        foreach ($assignments as $assignment) {
            $teacher = $teachers->where('nom', $assignment['teacher_nom'])
                                ->where('prenom', $assignment['teacher_prenom'])
                                ->first();
            
            if (!$teacher) {
                $this->command->warn("Teacher {$assignment['teacher_prenom']} {$assignment['teacher_nom']} not found.");
                continue;
            }

            foreach ($assignment['subjects'] as $subjectCode) {
                $matiere = $matieres->where('code_matiere', $subjectCode)->first();
                
                if (!$matiere) {
                    $this->command->warn("Subject with code {$subjectCode} not found.");
                    continue;
                }

                foreach ($assignment['classes'] as $className) {
                    $classe = $classes->where('nom_classe', $className)->first();
                    
                    if (!$classe) {
                        $this->command->warn("Class {$className} not found.");
                        continue;
                    }

                    // Insert the assignment if it doesn't exist
                    DB::table('enseignant_matiere_classe')->insertOrIgnore([
                        'id_enseignant' => $teacher->id_enseignant,
                        'id_matiere' => $matiere->id_matiere,
                        'id_classe' => $classe->id_classe,
                        'active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("Assigned {$teacher->name} to teach {$matiere->nom_matiere} in {$classe->nom_classe}");
                }  
            }
        }

        $totalAssignments = DB::table('enseignant_matiere_classe')->count();
        $this->command->info("✅ All {$teachers->count()} teachers assigned! Total assignments: {$totalAssignments}");
    }
}
