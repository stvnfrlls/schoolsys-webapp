<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Each entry maps a seeder class to the table that "owns" it.
     * The seeder is skipped when its table already has at least one row.
     */
    private array $seeders = [
        RolePermissionSeeder::class  => 'roles',
        UserSeeder::class            => 'users',
        GradeLevelSeeder::class      => 'grade_levels',
        SectionSeeder::class         => 'sections',
        SubjectSeeder::class         => 'subjects',
        SubjectPerLevelSeeder::class => 'subject_per_levels',
        SchoolYearSeeder::class      => 'school_years',
        FacultySeeder::class         => 'faculties',
        StudentSeeder::class         => 'students',
        EnrollmentSeeder::class      => 'enrollments',
        ScheduleSeeder::class        => 'schedules',
    ];

    public function run(): void
    {
       

        foreach ($this->seeders as $seeder => $table) {
            if (DB::table($table)->exists()) {
                $this->command->info("Skipping {$seeder}: [{$table}] is not empty.");
                continue;
            }

            $this->command->info("Running {$seeder} …");
            $this->call($seeder);
        }
    }
}
