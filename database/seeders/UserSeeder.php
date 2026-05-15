<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name'              => 'System Administrator',
            'email'             => 'admin@school.edu.ph',
            'password'          => Hash::make('password'),
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Faculty users — matches FacultySeeder order exactly
        $facultyUsers = [
            ['name' => 'Juan dela Cruz',        'email' => 'jdelacruz@school.edu.ph'],
            ['name' => 'Maria Santos',           'email' => 'msantos@school.edu.ph'],
            ['name' => 'Ricardo Castillo',       'email' => 'rcastillo@school.edu.ph'],
            ['name' => 'Jose Reyes',             'email' => 'jreyes@school.edu.ph'],
            ['name' => 'Ana Garcia',             'email' => 'agarcia@school.edu.ph'],
            ['name' => 'Pedro Flores',           'email' => 'pflores@school.edu.ph'],
            ['name' => 'Rosa Mendoza',           'email' => 'rmendoza@school.edu.ph'],
            ['name' => 'Fernando Cruz',          'email' => 'fcruz@school.edu.ph'],
            ['name' => 'Carlos Torres',          'email' => 'ctorres@school.edu.ph'],
            ['name' => 'Luz Ramos',              'email' => 'lramos@school.edu.ph'],
            ['name' => 'Gloria Aquino',          'email' => 'gaquino@school.edu.ph'],
            ['name' => 'Carmen Bautista',        'email' => 'cbautista@school.edu.ph'],
            ['name' => 'Natividad Lim',          'email' => 'nlim@school.edu.ph'],
            ['name' => 'Miguel Lopez',           'email' => 'mlopez@school.edu.ph'],
            ['name' => 'Elena Gonzales',         'email' => 'egonzales@school.edu.ph'],
            ['name' => 'Roberto Villanueva',     'email' => 'rvillanueva@school.edu.ph'],
            ['name' => 'Josephine Dela Rosa',    'email' => 'jdelarosa@school.edu.ph'],
            ['name' => 'Emmanuel Santos',        'email' => 'esantos@school.edu.ph'],
            ['name' => 'Maricel Navarro',        'email' => 'mnavarro@school.edu.ph'],
            ['name' => 'Antonio Reyes',          'email' => 'areyes@school.edu.ph'],
        ];

        foreach ($facultyUsers as $data) {
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make('password'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
            $user->assignRole('Faculty');
        }

        // Student users — 60 students (10 per grade level)
        $studentNames = [
            // Grade 7
            'Andrei Bautista',
            'Sofia Reyes',
            'Marco dela Cruz',
            'Isabelle Santos',
            'Rafael Garcia',
            'Camille Torres',
            'Gabriel Flores',
            'Katrina Lopez',
            'Justin Mendoza',
            'Ysabel Aquino',
            // Grade 8
            'Daniel Ramos',
            'Angela Cruz',
            'Adrian Villanueva',
            'Bianca Castillo',
            'Nathan Lim',
            'Patricia Santos',
            'Ian Gonzales',
            'Janella dela Rosa',
            'Kevin Navarro',
            'Alyssa Reyes',
            // Grade 9
            'Christian Torres',
            'Francesca Garcia',
            'Joshua Flores',
            'Lovely Bautista',
            'Mark Lopez',
            'Nicole Mendoza',
            'Paolo Aquino',
            'Rachelle Ramos',
            'Samuel Cruz',
            'Trixie Castillo',
            // Grade 10
            'Aldrich Lim',
            'Beatrice Santos',
            'Cedric Gonzales',
            'Diana dela Rosa',
            'Erwin Navarro',
            'Faith Reyes',
            'Gilbert Torres',
            'Hannah Garcia',
            'Ivan Flores',
            'Jasmine Bautista',
            // Grade 11
            'Kenneth Lopez',
            'Lorraine Mendoza',
            'Michael Aquino',
            'Nina Ramos',
            'Oliver Cruz',
            'Pearl Villanueva',
            'Quincy Castillo',
            'Roxanne Lim',
            'Stefan Santos',
            'Trisha Gonzales',
            // Grade 12
            'Ulysses dela Rosa',
            'Vanessa Navarro',
            'Winston Reyes',
            'Ximena Torres',
            'Yves Garcia',
            'Zoraida Flores',
            'Aaron Bautista',
            'Bella Lopez',
            'Carlos Mendoza',
            'Danica Aquino',
        ];

        foreach ($studentNames as $i => $name) {
            $user = User::create([
                'name'              => $name,
                'email'             => 'student' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '@school.edu.ph',
                'password'          => Hash::make('password'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
            $user->assignRole('Student');
        }
    }
}
