<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sectionNames = ['Sampaguita', 'Rosal', 'Ilang-Ilang'];

        GradeLevel::all()->each(function (GradeLevel $gradeLevel) use ($sectionNames) {
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
