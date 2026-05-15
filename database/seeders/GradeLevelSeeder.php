<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;

class GradeLevelSeeder extends Seeder
{
    public function run(): void
    {
        $gradeLevels = [
            ['name' => 'Grade 7',  'level' => 7,  'is_active' => 'active'],
            ['name' => 'Grade 8',  'level' => 8,  'is_active' => 'active'],
            ['name' => 'Grade 9',  'level' => 9,  'is_active' => 'active'],
            ['name' => 'Grade 10', 'level' => 10, 'is_active' => 'active'],
            ['name' => 'Grade 11', 'level' => 11, 'is_active' => 'active'],
            ['name' => 'Grade 12', 'level' => 12, 'is_active' => 'active'],
        ];

        foreach ($gradeLevels as $data) {
            GradeLevel::create($data);
        }
    }
}
