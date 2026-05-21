<?php

namespace Database\Seeders;

use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EtudiantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = Classe::all();
        
        if ($classes->isEmpty()) {
            $this->command->warn('No classes found. Run ClassesSeeder first.');
            return;
        }

        // Sample of realistic students for demonstration
        $this->createSampleStudents($classes);
        
        // Generate additional random students for each class
        $this->generateRandomStudents($classes);
        
        $this->command->info('✅ Created ' . Etudiant::count() . ' students with matricules');
    }
    
    private function createSampleStudents($classes): void
    {
        $sampleStudents = [
            // CP Students (Age 6-7)
            [
                'nom' => 'Youssouf',
                'prenom' => 'Mahamat',
                'telephone' => '+235 00 11 22 33',
                'adresse' => 'Ndjaména, Tchad',
                'date_naissance' => '2002-05-08',
                'genre' => 'M',
                'classe' => 'Licence L3',
                'has_account' => true,
                'email' => 'youssouf@etu.toumai.com',
            ],
            [
                'nom' => 'Saleh',
                'prenom' => 'Oumar',
                'telephone' => '+235 00 01 22 33',
                'adresse' => 'Ndjaména, Tchad',
                'date_naissance' => '2006-06-28',
                'genre' => 'M',
                'classe' => 'Licence L1',
                'has_account' => true,
                'email' => 'oumar@etu.toumai.com',
            ],
            
            // CM2 Students (Age 10-11)
            [
                'nom' => 'Moussa',
                'prenom' => 'Khadidja',
                'telephone' => '+235 00 10 22 33',
                'adresse' => 'Ndjaména, Tchad',
                'date_naissance' => '2005-10-13',
                'genre' => 'F',
                'classe' => 'Licence L2',
                'has_account' => true,
                'email' => 'khadidja@etu.toumai.com',
            ],
        ];
        
        foreach ($sampleStudents as $studentData) {
            $classe = $classes->where('nom_classe', $studentData['classe'])->first();
            if (!$classe) continue;

            // Avoid creating duplicate student records
            $existingEtudiant = Etudiant::where('nom', $studentData['nom'])
                ->where('prenom', $studentData['prenom'])
                ->where('id_classe', $classe->id_classe)
                ->first();

            if ($existingEtudiant) {
                $etudiant = $existingEtudiant;
                $this->command->info('Skipping existing student: ' . $studentData['prenom'] . ' ' . $studentData['nom']);
            } else {
                // Create student
                $etudiant = Etudiant::create([
                    'nom' => $studentData['nom'],
                    'prenom' => $studentData['prenom'], 
                    'telephone' => $studentData['telephone'],
                    'adresse' => $studentData['adresse'],
                    'date_naissance' => $studentData['date_naissance'],
                    'genre' => $studentData['genre'],
                    'id_classe' => $classe->id_classe,
                    // matricule will be auto-generated
                ]);
            }

            // Create user account if specified and email not already used
            if ($studentData['has_account'] && isset($studentData['email'])) {
                if (!User::where('email', $studentData['email'])->exists()) {
                    $user = User::create([
                        'name' => trim($studentData['prenom'] . ' ' . $studentData['nom']),
                        'email' => $studentData['email'],
                        'password' => Hash::make('student123'),
                        'is_active' => true,
                        'email_verified_at' => now(),
                        'profile_type' => Etudiant::class,
                        'profile_id' => $etudiant->id_etudiant,
                    ]);
                    
                    $user->assignRole('student');
                } else {
                    $this->command->info('Skipping existing student user: ' . $studentData['email']);
                }
            }
        }
    }
    
    private function generateRandomStudents($classes): void
    {
        foreach ($classes as $classe) {
            // Generate 8-15 students per class
            $studentsCount = rand(8, 15);
            
            for ($i = 0; $i < $studentsCount; $i++) {
                $genre = rand(1, 2) == 1 ? 'M' : 'F';
                $nom = $this->getRandomChadianLastName();
                $prenom = $this->getRandomChadianFirstName($genre);
                $birthDate = $this->calculateBirthDate($classe->niveau);
                
                // Only create accounts for older students (niveau >= 12)
                $hasAccount = $classe->niveau >= 12 && rand(1, 100) <= 30; // 30% chance
                
                // Avoid creating duplicate student entries by name + class
                if (Etudiant::where('nom', $nom)->where('prenom', $prenom)->where('id_classe', $classe->id_classe)->exists()) {
                    $this->command->info('Skipping duplicate generated student: ' . $prenom . ' ' . $nom . ' (' . $classe->nom_classe . ')');
                    continue;
                }

                $etudiant = Etudiant::create([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'telephone' => $this->generatePhoneNumber(),
                    'adresse' => $this->getRandomAddress(),
                    'date_naissance' => $birthDate,
                    'genre' => $genre,
                    'id_classe' => $classe->id_classe,
                ]);
                
                // Create user account if needed
                if ($hasAccount) {
                    $email = strtolower($prenom) . '.' . strtolower(str_replace(' ', '', $nom)) . '@student.ecole.com';
                    $email = $this->ensureUniqueEmail($email);
                    
                    $user = User::create([
                        'name' => trim($prenom . ' ' . $nom),
                        'email' => $email,
                        'password' => Hash::make('student123'),
                        'is_active' => rand(1, 100) <= 90, // 90% active
                        'email_verified_at' => now(),
                        'profile_type' => Etudiant::class,
                        'profile_id' => $etudiant->id_etudiant,
                    ]);
                    
                    $user->assignRole('student');
                }
            }
        }
    }
    
    private function getRandomChadianFirstName(string $genre): string
    {
        $maleNames = [
            'Mahamat', 'Ahmat', 'Saleh', 'Ousman', 'Abdallah'
            ];
        
        $femaleNames = [
            'Fatimé', 'Aicha', 'Hawa', 'Zara'
        ];
        
        return $genre === 'M' ? $maleNames[array_rand($maleNames)] : $femaleNames[array_rand($femaleNames)];
    }
    
    private function getRandomChadianLastName(): string
    {
        $lastNames = [
            'Oumar Ahmat', 'Moussa Abakar', 'Ali', 'Saleh'
         ];
        
        return $lastNames[array_rand($lastNames)];
    }
    
    private function getRandomAddress(): string
    {
        $neighborhoods = [
            'Ville de Ndjaména'
        ];
        
        $streets = [
            'Quartier %d', 'Secteur %d', 'Bloc %s', 'Ilot %s', 'Villa %d', 'Lot %d',
            'Rue %s', 'Avenue %s'
        ];
        
        $neighborhood = $neighborhoods[array_rand($neighborhoods)];
        $street = sprintf($streets[array_rand($streets)], 
            in_array('%s', [$streets[array_rand($streets)]]) ? chr(65 + rand(0, 10)) : rand(1, 500)
        );
        
        return $neighborhood . ', ' . $street . ', Ndjaména';
    }
    
    private function generatePhoneNumber(): string
    {
        $prefixes = ['30', '31', '32', '33', '34', '36', '37', '38', '39'];
        $prefix = $prefixes[array_rand($prefixes)];
        $number = sprintf('%02d %02d %02d', rand(10, 99), rand(10, 99), rand(10, 99));
        
        return '+235 ' . $prefix . ' ' . $number;
    }
    
    private function calculateBirthDate(int $niveau): string
    {
        // Calculate appropriate age based on grade level
        $baseAge = $niveau + 2; // Approximate age formula
        $currentYear = date('Y');
        $birthYear = $currentYear - $baseAge + rand(-1, 1); // Add some variation
        
        $month = rand(1, 12);
        $day = rand(1, 28);
        
        return sprintf('%d-%02d-%02d', $birthYear, $month, $day);
    }
    
    private function ensureUniqueEmail(string $email): string
    {
        $originalEmail = $email;
        $counter = 1;
        
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@', $counter . '@', $originalEmail);
            $counter++;
        }
        
        return $email;
    }
}
