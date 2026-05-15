<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear   = SchoolYear::where('is_active', 'active')->first();
        $previousYear = SchoolYear::where('name', '2023-2024')->first();

        // Students are grouped 10 per grade (Grades 7–12), in StudentSeeder order
        // Grade 7 → students 1–10, Grade 8 → 11–20, ..., Grade 12 → 51–60
        $gradeLevels = GradeLevel::orderBy('level')->get();

        foreach ($gradeLevels as $gradeIndex => $gradeLevel) {
            $sections = Section::where('grade_level_id', $gradeLevel->id)
                ->orderBy('id')
                ->get();

            // Pull the 10 students for this grade level
            $offset   = $gradeIndex * 10 + 1;
            $students = Student::skip($offset - 1)->take(10)->get();

            // Distribute across 3 sections: 4, 3, 3
            $distribution = [4, 3, 3];

            $studentPointer = 0;
            foreach ($sections as $sectionIndex => $section) {
                $count = $distribution[$sectionIndex] ?? 3;
                $room  = 'Room ' . $gradeLevel->level . '0' . ($sectionIndex + 1);

                for ($i = 0; $i < $count; $i++) {
                    $student = $students[$studentPointer] ?? null;
                    if (! $student) {
                        break;
                    }

                    // Active year enrollment (2024–2025)
                    Enrollment::firstOrCreate(
                        [
                            'student_id'     => $student->id,
                            'school_year_id' => $activeYear->id,
                        ],
                        [
                            'section_id' => $section->id,
                            'status'     => 'enrolled',
                            'enrolled_at' => now()->subMonths(rand(1, 4))->format('Y-m-d'),
                        ]
                    );

                    // Previous year enrollment (2023–2024) for Grades 8–12
                    // They were in the grade below the previous year
                    if ($previousYear && $gradeLevel->level >= 8) {
                        $prevGradeLevel = GradeLevel::where('level', $gradeLevel->level - 1)->first();
                        if ($prevGradeLevel) {
                            $prevSection = Section::where('grade_level_id', $prevGradeLevel->id)
                                ->skip($sectionIndex)
                                ->first();

                            if ($prevSection) {
                                Enrollment::firstOrCreate(
                                    [
                                        'student_id'     => $student->id,
                                        'school_year_id' => $previousYear->id,
                                    ],
                                    [
                                        'section_id'  => $prevSection->id,
                                        'status'      => 'completed',
                                        'enrolled_at' => '2023-08-14',
                                    ]
                                );
                            }
                        }
                    }

                    $studentPointer++;
                }
            }
        }
    }
}
