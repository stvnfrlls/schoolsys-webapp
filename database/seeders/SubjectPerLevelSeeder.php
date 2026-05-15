<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\SubjectPerLevel;
use Illuminate\Database\Seeder;

class SubjectPerLevelSeeder extends Seeder
{
    public function run(): void
    {
        // JHS subjects with hours_per_week (Grades 7–10)
        $jhsSubjects = [
            'Mathematics'                       => 5,
            'Science'                           => 4,
            'English'                           => 5,
            'Filipino'                          => 4,
            'MAPEH'                             => 3,
            'Araling Panlipunan'                => 2,
            'Technology and Livelihood Education' => 2,
            'Edukasyon sa Pagpapakatao'         => 2,
        ];

        // SHS subjects with hours_per_week (Grades 11–12)
        $shsSubjects = [
            'General Mathematics'        => 4,
            'Statistics and Probability' => 3,
            'Physical Science'           => 4,
            'Earth and Life Science'     => 3,
            'Oral Communication'         => 3,
            'Reading and Writing'        => 3,
            '21st Century Literature'    => 2,
            'Personal Development'       => 2,
            'Media and Information Literacy' => 2,
        ];

        $jhsGrades = GradeLevel::whereIn('level', [7, 8, 9, 10])->get()->keyBy('id');
        $shsGrades = GradeLevel::whereIn('level', [11, 12])->get()->keyBy('id');

        $this->assignSubjects($jhsGrades, $jhsSubjects);
        $this->assignSubjects($shsGrades, $shsSubjects);
    }

    private function assignSubjects($gradeLevels, array $subjectHoursMap): void
    {
        foreach ($gradeLevels as $gradeLevel) {
            foreach ($subjectHoursMap as $subjectName => $hours) {
                $subject = Subject::where('name', $subjectName)->first();
                if (! $subject) {
                    continue;
                }

                SubjectPerLevel::firstOrCreate(
                    [
                        'gradelevel_id' => $gradeLevel->id,
                        'subject_id'    => $subject->id,
                    ],
                    [
                        'hours_per_week' => $hours,
                        'is_active'      => 'active',
                    ]
                );
            }
        }
    }
}
