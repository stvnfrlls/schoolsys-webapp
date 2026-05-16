<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sectionsByGrade = [
            'Grade 7' => ['Sampaguita', 'Rosal', 'Ilang-Ilang'],
            'Grade 8' => ['Aurora', 'Benilson', 'Calamansi'],
            'Grade 9' => ['Dalahon', 'Edelweiss', 'Frangipani'],
            'Grade 10' => ['Gumamela', 'Heliotrope', 'Ixora'],
            'Grade 11' => ['Jasmine', 'Kalachoe', 'Lantana'],
            'Grade 12' => ['Marigold', 'Narcissus', 'Orchid'],
        ];

        GradeLevel::all()->each(function (GradeLevel $gradeLevel) use ($sectionsByGrade) {
            $sectionNames = $sectionsByGrade[$gradeLevel->name] ?? [];

            foreach ($sectionNames as $name) {
                Section::create([
                    'grade_level_id' => $gradeLevel->id,
                    'name'           => $name,
                    'is_active'      => 'active',
                ]);
            }
        });
    }
}
