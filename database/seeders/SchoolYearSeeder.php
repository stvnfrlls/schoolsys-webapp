<?php

namespace Database\Seeders;

use App\Models\SchoolYear;
use Illuminate\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYears = [
            [
                'name'       => '2022-2023',
                'start_date' => '2022-08-22',
                'end_date'   => '2023-06-30',
                'is_active'  => 'inactive',
            ],
            [
                'name'       => '2023-2024',
                'start_date' => '2023-08-14',
                'end_date'   => '2024-06-28',
                'is_active'  => 'inactive',
            ],
            [
                'name'       => '2024-2025',
                'start_date' => '2024-08-12',
                'end_date'   => '2025-06-27',
                'is_active'  => 'active',
            ],
        ];

        foreach ($schoolYears as $data) {
            SchoolYear::create($data);
        }
    }
}