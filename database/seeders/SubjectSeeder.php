<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // JHS subjects (Grades 7–10)
            [
                'name'        => 'Mathematics',
                'code'        => 'MATH-JHS',
                'description' => 'Core mathematics covering algebra, geometry, and statistics for junior high school.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Science',
                'code'        => 'SCI-JHS',
                'description' => 'Integrated science covering earth science, biology, chemistry, and physics.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'English',
                'code'        => 'ENG-JHS',
                'description' => 'English language arts including reading, writing, grammar, and literature.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Filipino',
                'code'        => 'FIL-JHS',
                'description' => 'Filipino language and literature for junior high school.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'MAPEH',
                'code'        => 'MAPEH-JHS',
                'description' => 'Music, Arts, Physical Education, and Health.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Araling Panlipunan',
                'code'        => 'AP-JHS',
                'description' => 'Social studies covering history, geography, economics, and civics.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Technology and Livelihood Education',
                'code'        => 'TLE-JHS',
                'description' => 'Practical skills in technology, home economics, agriculture, and industrial arts.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Edukasyon sa Pagpapakatao',
                'code'        => 'ESP-JHS',
                'description' => 'Values education and character formation.',
                'is_active'   => 'active',
            ],

            // SHS subjects (Grades 11–12)
            [
                'name'        => 'General Mathematics',
                'code'        => 'GMATH-SHS',
                'description' => 'Senior high school mathematics covering functions, business math, and logic.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Statistics and Probability',
                'code'        => 'STAT-SHS',
                'description' => 'Data analysis, probability theory, and statistical inference.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Physical Science',
                'code'        => 'PSCI-SHS',
                'description' => 'Fundamentals of physics and chemistry for senior high school.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Earth and Life Science',
                'code'        => 'ELS-SHS',
                'description' => 'Earth science, ecology, and life processes.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Oral Communication',
                'code'        => 'OCOM-SHS',
                'description' => 'Effective communication in context for academic and professional settings.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Reading and Writing',
                'code'        => 'RW-SHS',
                'description' => 'Academic reading and writing skills for senior high school.',
                'is_active'   => 'active',
            ],
            [
                'name'        => '21st Century Literature',
                'code'        => 'LIT-SHS',
                'description' => 'Philippine and world literature from the 21st century.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Personal Development',
                'code'        => 'PD-SHS',
                'description' => 'Self-awareness, mental health, and personal effectiveness.',
                'is_active'   => 'active',
            ],
            [
                'name'        => 'Media and Information Literacy',
                'code'        => 'MIL-SHS',
                'description' => 'Critical evaluation of media and information in the digital age.',
                'is_active'   => 'active',
            ],
        ];

        foreach ($subjects as $data) {
            Subject::create($data);
        }
    }
}
