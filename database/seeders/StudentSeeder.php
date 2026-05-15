<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // 60 students — 10 per grade level (Grades 7–12)
        // Indexed 0–59 to match UserSeeder student email order
        $students = [
            // Grade 7 (index 0–9) → student001–010
            ['first_name' => 'Andrei',    'middle_name' => 'Cruz',      'last_name' => 'Bautista',  'birth_date' => '2011-03-12', 'gender' => 'male',   'address' => '10 Rizal St., Manila',         'contact_number' => '09201110001', 'guardian_name' => 'Roberto Bautista',  'guardian_contact' => '09201110101', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Sofia',     'middle_name' => 'Lopez',     'last_name' => 'Reyes',     'birth_date' => '2011-06-25', 'gender' => 'female', 'address' => '22 Mabini St., Pasay',         'contact_number' => '09201110002', 'guardian_name' => 'Carla Reyes',       'guardian_contact' => '09201110102', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Marco',     'middle_name' => 'Santos',    'last_name' => 'dela Cruz', 'birth_date' => '2011-09-04', 'gender' => 'male',   'address' => '33 Luna St., Makati',          'contact_number' => '09201110003', 'guardian_name' => 'Jose dela Cruz',    'guardian_contact' => '09201110103', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Isabelle',  'middle_name' => 'Reyes',     'last_name' => 'Santos',    'birth_date' => '2011-01-18', 'gender' => 'female', 'address' => '44 Burgos St., Quezon City',   'contact_number' => '09201110004', 'guardian_name' => 'Linda Santos',      'guardian_contact' => '09201110104', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Rafael',    'middle_name' => 'Aquino',    'last_name' => 'Garcia',    'birth_date' => '2011-11-30', 'gender' => 'male',   'address' => '55 Taft Ave., Manila',         'contact_number' => '09201110005', 'guardian_name' => 'Manuel Garcia',     'guardian_contact' => '09201110105', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Camille',   'middle_name' => 'Bautista',  'last_name' => 'Torres',    'birth_date' => '2011-04-07', 'gender' => 'female', 'address' => '66 España Blvd., Manila',      'contact_number' => '09201110006', 'guardian_name' => 'Alma Torres',       'guardian_contact' => '09201110106', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Gabriel',   'middle_name' => 'Garcia',    'last_name' => 'Flores',    'birth_date' => '2011-07-22', 'gender' => 'male',   'address' => '77 Shaw Blvd., Mandaluyong',   'contact_number' => '09201110007', 'guardian_name' => 'Ernesto Flores',    'guardian_contact' => '09201110107', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Katrina',   'middle_name' => 'dela Cruz', 'last_name' => 'Lopez',     'birth_date' => '2011-02-14', 'gender' => 'female', 'address' => '88 Katipunan Ave., QC',        'contact_number' => '09201110008', 'guardian_name' => 'Norma Lopez',       'guardian_contact' => '09201110108', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Justin',    'middle_name' => 'Torres',    'last_name' => 'Mendoza',   'birth_date' => '2011-10-05', 'gender' => 'male',   'address' => '99 EDSA, Caloocan',            'contact_number' => '09201110009', 'guardian_name' => 'Victor Mendoza',    'guardian_contact' => '09201110109', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Ysabel',    'middle_name' => 'Flores',    'last_name' => 'Aquino',    'birth_date' => '2011-08-19', 'gender' => 'female', 'address' => '12 Aurora Blvd., Cubao',       'contact_number' => '09201110010', 'guardian_name' => 'Cecilia Aquino',    'guardian_contact' => '09201110110', 'guardian_relationship' => 'Mother'],

            // Grade 8 (index 10–19) → student011–020
            ['first_name' => 'Daniel',    'middle_name' => 'Santos',    'last_name' => 'Ramos',     'birth_date' => '2010-05-11', 'gender' => 'male',   'address' => '15 Tandang Sora, QC',          'contact_number' => '09201110011', 'guardian_name' => 'Felix Ramos',       'guardian_contact' => '09201110111', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Angela',    'middle_name' => 'Lopez',     'last_name' => 'Cruz',      'birth_date' => '2010-09-28', 'gender' => 'female', 'address' => '27 Padre Faura, Ermita',       'contact_number' => '09201110012', 'guardian_name' => 'Rosario Cruz',      'guardian_contact' => '09201110112', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Adrian',    'middle_name' => 'Reyes',     'last_name' => 'Villanueva', 'birth_date' => '2010-01-03', 'gender' => 'male',   'address' => '38 Adriatico St., Malate',     'contact_number' => '09201110013', 'guardian_name' => 'Homer Villanueva',  'guardian_contact' => '09201110113', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Bianca',    'middle_name' => 'Garcia',    'last_name' => 'Castillo',  'birth_date' => '2010-12-16', 'gender' => 'female', 'address' => '49 Arnaiz Ave., Makati',       'contact_number' => '09201110014', 'guardian_name' => 'Teresita Castillo', 'guardian_contact' => '09201110114', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Nathan',    'middle_name' => 'Cruz',      'last_name' => 'Lim',       'birth_date' => '2010-03-27', 'gender' => 'male',   'address' => '60 Gilmore Ave., San Juan',    'contact_number' => '09201110015', 'guardian_name' => 'Andrew Lim',        'guardian_contact' => '09201110115', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Patricia',  'middle_name' => 'Aquino',    'last_name' => 'Santos',    'birth_date' => '2010-07-08', 'gender' => 'female', 'address' => '71 Ortigas Ave., Pasig',       'contact_number' => '09201110016', 'guardian_name' => 'Mario Santos',      'guardian_contact' => '09201110116', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Ian',       'middle_name' => 'Bautista',  'last_name' => 'Gonzales',  'birth_date' => '2010-11-20', 'gender' => 'male',   'address' => '82 Del Pilar St., Manila',     'contact_number' => '09201110017', 'guardian_name' => 'Pacita Gonzales',   'guardian_contact' => '09201110117', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Janella',   'middle_name' => 'Torres',    'last_name' => 'dela Rosa', 'birth_date' => '2010-04-14', 'gender' => 'female', 'address' => '93 Blumentritt St., Manila',   'contact_number' => '09201110018', 'guardian_name' => 'Rudy dela Rosa',    'guardian_contact' => '09201110118', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Kevin',     'middle_name' => 'Flores',    'last_name' => 'Navarro',   'birth_date' => '2010-06-02', 'gender' => 'male',   'address' => '4 C.M. Recto Ave., Manila',    'contact_number' => '09201110019', 'guardian_name' => 'Eddie Navarro',     'guardian_contact' => '09201110119', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Alyssa',    'middle_name' => 'Mendoza',   'last_name' => 'Reyes',     'birth_date' => '2010-08-31', 'gender' => 'female', 'address' => '16 Tomas Morato, QC',          'contact_number' => '09201110020', 'guardian_name' => 'Grace Reyes',       'guardian_contact' => '09201110120', 'guardian_relationship' => 'Mother'],

            // Grade 9 (index 20–29)
            ['first_name' => 'Christian', 'middle_name' => 'Lim',       'last_name' => 'Torres',    'birth_date' => '2009-02-17', 'gender' => 'male',   'address' => '28 España Ext., Manila',       'contact_number' => '09201110021', 'guardian_name' => 'Ronald Torres',     'guardian_contact' => '09201110121', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Francesca', 'middle_name' => 'Santos',    'last_name' => 'Garcia',    'birth_date' => '2009-10-09', 'gender' => 'female', 'address' => '39 Zobel Roxas St., Manila',   'contact_number' => '09201110022', 'guardian_name' => 'Lourdes Garcia',    'guardian_contact' => '09201110122', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Joshua',    'middle_name' => 'Reyes',     'last_name' => 'Flores',    'birth_date' => '2009-05-24', 'gender' => 'male',   'address' => '50 Bambang St., Manila',       'contact_number' => '09201110023', 'guardian_name' => 'Alfredo Flores',    'guardian_contact' => '09201110123', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Lovely',    'middle_name' => 'Cruz',      'last_name' => 'Bautista',  'birth_date' => '2009-12-01', 'gender' => 'female', 'address' => '61 Nagtahan St., Manila',      'contact_number' => '09201110024', 'guardian_name' => 'Dolores Bautista',  'guardian_contact' => '09201110124', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Mark',      'middle_name' => 'Garcia',    'last_name' => 'Lopez',     'birth_date' => '2009-03-15', 'gender' => 'male',   'address' => '72 Legarda St., Manila',       'contact_number' => '09201110025', 'guardian_name' => 'Dennis Lopez',      'guardian_contact' => '09201110125', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Nicole',    'middle_name' => 'Aquino',    'last_name' => 'Mendoza',   'birth_date' => '2009-07-28', 'gender' => 'female', 'address' => '83 Mayon St., QC',             'contact_number' => '09201110026', 'guardian_name' => 'Vivian Mendoza',    'guardian_contact' => '09201110126', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Paolo',     'middle_name' => 'Torres',    'last_name' => 'Aquino',    'birth_date' => '2009-11-06', 'gender' => 'male',   'address' => '94 Mindanao Ave., QC',         'contact_number' => '09201110027', 'guardian_name' => 'Ben Aquino',        'guardian_contact' => '09201110127', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Rachelle',  'middle_name' => 'Villanueva', 'last_name' => 'Ramos',     'birth_date' => '2009-01-22', 'gender' => 'female', 'address' => '5 Novaliches Rd., Caloocan',   'contact_number' => '09201110028', 'guardian_name' => 'Perla Ramos',       'guardian_contact' => '09201110128', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Samuel',    'middle_name' => 'dela Rosa', 'last_name' => 'Cruz',      'birth_date' => '2009-09-13', 'gender' => 'male',   'address' => '17 University Ave., QC',       'contact_number' => '09201110029', 'guardian_name' => 'Arturo Cruz',       'guardian_contact' => '09201110129', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Trixie',    'middle_name' => 'Navarro',   'last_name' => 'Castillo',  'birth_date' => '2009-04-30', 'gender' => 'female', 'address' => '29 Scout Tobias, QC',          'contact_number' => '09201110030', 'guardian_name' => 'Rowena Castillo',   'guardian_contact' => '09201110130', 'guardian_relationship' => 'Mother'],

            // Grade 10 (index 30–39)
            ['first_name' => 'Aldrich',   'middle_name' => 'Santos',    'last_name' => 'Lim',       'birth_date' => '2008-06-18', 'gender' => 'male',   'address' => '41 Bohol Ave., QC',            'contact_number' => '09201110031', 'guardian_name' => 'Noel Lim',          'guardian_contact' => '09201110131', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Beatrice',  'middle_name' => 'Lopez',     'last_name' => 'Santos',    'birth_date' => '2008-02-05', 'gender' => 'female', 'address' => '52 Maginoo St., QC',           'contact_number' => '09201110032', 'guardian_name' => 'Flora Santos',      'guardian_contact' => '09201110132', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Cedric',    'middle_name' => 'Reyes',     'last_name' => 'Gonzales',  'birth_date' => '2008-10-23', 'gender' => 'male',   'address' => '63 Batangas St., Manila',      'contact_number' => '09201110033', 'guardian_name' => 'Oscar Gonzales',    'guardian_contact' => '09201110133', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Diana',     'middle_name' => 'Cruz',      'last_name' => 'dela Rosa', 'birth_date' => '2008-08-11', 'gender' => 'female', 'address' => '74 Sampaloc, Manila',          'contact_number' => '09201110034', 'guardian_name' => 'Luz dela Rosa',     'guardian_contact' => '09201110134', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Erwin',     'middle_name' => 'Garcia',    'last_name' => 'Navarro',   'birth_date' => '2008-04-16', 'gender' => 'male',   'address' => '85 Pedro Gil St., Manila',     'contact_number' => '09201110035', 'guardian_name' => 'Danny Navarro',     'guardian_contact' => '09201110135', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Faith',     'middle_name' => 'Aquino',    'last_name' => 'Reyes',     'birth_date' => '2008-12-29', 'gender' => 'female', 'address' => '96 Libertad St., Pasay',       'contact_number' => '09201110036', 'guardian_name' => 'Nelia Reyes',       'guardian_contact' => '09201110136', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Gilbert',   'middle_name' => 'Bautista',  'last_name' => 'Torres',    'birth_date' => '2008-07-07', 'gender' => 'male',   'address' => '7 Quirino Ave., Paranaque',    'contact_number' => '09201110037', 'guardian_name' => 'Eduardo Torres',    'guardian_contact' => '09201110137', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Hannah',    'middle_name' => 'Torres',    'last_name' => 'Garcia',    'birth_date' => '2008-03-24', 'gender' => 'female', 'address' => '18 San Andres St., Manila',    'contact_number' => '09201110038', 'guardian_name' => 'Nenita Garcia',     'guardian_contact' => '09201110138', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Ivan',      'middle_name' => 'Mendoza',   'last_name' => 'Flores',    'birth_date' => '2008-09-19', 'gender' => 'male',   'address' => '30 Rodriguez St., Manila',     'contact_number' => '09201110039', 'guardian_name' => 'Greg Flores',       'guardian_contact' => '09201110139', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Jasmine',   'middle_name' => 'Ramos',     'last_name' => 'Bautista',  'birth_date' => '2008-01-08', 'gender' => 'female', 'address' => '42 Masangkay St., Manila',     'contact_number' => '09201110040', 'guardian_name' => 'Cynthia Bautista',  'guardian_contact' => '09201110140', 'guardian_relationship' => 'Mother'],

            // Grade 11 (index 40–49)
            ['first_name' => 'Kenneth',   'middle_name' => 'Cruz',      'last_name' => 'Lopez',     'birth_date' => '2007-05-03', 'gender' => 'male',   'address' => '53 Anda St., Manila',          'contact_number' => '09201110041', 'guardian_name' => 'Rodolfo Lopez',     'guardian_contact' => '09201110141', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Lorraine',  'middle_name' => 'Santos',    'last_name' => 'Mendoza',   'birth_date' => '2007-11-17', 'gender' => 'female', 'address' => '64 Lacson Ave., Manila',       'contact_number' => '09201110042', 'guardian_name' => 'Milagros Mendoza',  'guardian_contact' => '09201110142', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Michael',   'middle_name' => 'Reyes',     'last_name' => 'Aquino',    'birth_date' => '2007-08-26', 'gender' => 'male',   'address' => '75 Retiro St., Manila',        'contact_number' => '09201110043', 'guardian_name' => 'Virgilio Aquino',   'guardian_contact' => '09201110143', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Nina',      'middle_name' => 'Garcia',    'last_name' => 'Ramos',     'birth_date' => '2007-02-12', 'gender' => 'female', 'address' => '86 Tayuman St., Manila',       'contact_number' => '09201110044', 'guardian_name' => 'Teresa Ramos',      'guardian_contact' => '09201110144', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Oliver',    'middle_name' => 'Lim',       'last_name' => 'Cruz',      'birth_date' => '2007-06-30', 'gender' => 'male',   'address' => '97 Andalucia St., Manila',     'contact_number' => '09201110045', 'guardian_name' => 'Nelson Cruz',       'guardian_contact' => '09201110145', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Pearl',     'middle_name' => 'Aquino',    'last_name' => 'Villanueva', 'birth_date' => '2007-09-21', 'gender' => 'female', 'address' => '8 Oroquieta St., Manila',      'contact_number' => '09201110046', 'guardian_name' => 'Corazon Villanueva', 'guardian_contact' => '09201110146', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Quincy',    'middle_name' => 'Torres',    'last_name' => 'Castillo',  'birth_date' => '2007-04-07', 'gender' => 'male',   'address' => '20 Florentino Torres, Manila', 'contact_number' => '09201110047', 'guardian_name' => 'Ramon Castillo',    'guardian_contact' => '09201110147', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Roxanne',   'middle_name' => 'dela Cruz', 'last_name' => 'Lim',       'birth_date' => '2007-12-14', 'gender' => 'female', 'address' => '31 Sta. Mesa Blvd., Manila',  'contact_number' => '09201110048', 'guardian_name' => 'Ester Lim',         'guardian_contact' => '09201110148', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Stefan',    'middle_name' => 'Flores',    'last_name' => 'Santos',    'birth_date' => '2007-07-09', 'gender' => 'male',   'address' => '43 Dapitan St., Manila',       'contact_number' => '09201110049', 'guardian_name' => 'Philip Santos',     'guardian_contact' => '09201110149', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Trisha',    'middle_name' => 'Navarro',   'last_name' => 'Gonzales',  'birth_date' => '2007-03-25', 'gender' => 'female', 'address' => '54 Lachambre St., Manila',     'contact_number' => '09201110050', 'guardian_name' => 'Elma Gonzales',     'guardian_contact' => '09201110150', 'guardian_relationship' => 'Mother'],

            // Grade 12 (index 50–59)
            ['first_name' => 'Ulysses',   'middle_name' => 'Santos',    'last_name' => 'dela Rosa', 'birth_date' => '2006-10-01', 'gender' => 'male',   'address' => '65 Pavia St., Manila',         'contact_number' => '09201110051', 'guardian_name' => 'Alfredo dela Rosa',  'guardian_contact' => '09201110151', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Vanessa',   'middle_name' => 'Lopez',     'last_name' => 'Navarro',   'birth_date' => '2006-04-18', 'gender' => 'female', 'address' => '76 Palanca St., Manila',       'contact_number' => '09201110052', 'guardian_name' => 'Helen Navarro',     'guardian_contact' => '09201110152', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Winston',   'middle_name' => 'Cruz',      'last_name' => 'Reyes',     'birth_date' => '2006-08-27', 'gender' => 'male',   'address' => '87 Dasmariñas St., Manila',    'contact_number' => '09201110053', 'guardian_name' => 'Rodrigo Reyes',     'guardian_contact' => '09201110153', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Ximena',    'middle_name' => 'Reyes',     'last_name' => 'Torres',    'birth_date' => '2006-01-14', 'gender' => 'female', 'address' => '98 Rosario St., Binondo',      'contact_number' => '09201110054', 'guardian_name' => 'Pilar Torres',      'guardian_contact' => '09201110154', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Yves',      'middle_name' => 'Aquino',    'last_name' => 'Garcia',    'birth_date' => '2006-05-05', 'gender' => 'male',   'address' => '9 Quintin Paredes, Manila',    'contact_number' => '09201110055', 'guardian_name' => 'Benjamin Garcia',   'guardian_contact' => '09201110155', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Zoraida',   'middle_name' => 'Bautista',  'last_name' => 'Flores',    'birth_date' => '2006-11-22', 'gender' => 'female', 'address' => '21 Calle Real, Intramuros',    'contact_number' => '09201110056', 'guardian_name' => 'Herminia Flores',   'guardian_contact' => '09201110156', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Aaron',     'middle_name' => 'Garcia',    'last_name' => 'Bautista',  'birth_date' => '2006-07-16', 'gender' => 'male',   'address' => '32 General Luna St., Manila',  'contact_number' => '09201110057', 'guardian_name' => 'Gilbert Bautista',  'guardian_contact' => '09201110157', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Bella',     'middle_name' => 'Torres',    'last_name' => 'Lopez',     'birth_date' => '2006-02-08', 'gender' => 'female', 'address' => '44 Anda Circle, Manila',       'contact_number' => '09201110058', 'guardian_name' => 'Lucila Lopez',      'guardian_contact' => '09201110158', 'guardian_relationship' => 'Mother'],
            ['first_name' => 'Carlos',    'middle_name' => 'Villanueva', 'last_name' => 'Mendoza',   'birth_date' => '2006-09-03', 'gender' => 'male',   'address' => '55 Solana St., Manila',        'contact_number' => '09201110059', 'guardian_name' => 'Armando Mendoza',   'guardian_contact' => '09201110159', 'guardian_relationship' => 'Father'],
            ['first_name' => 'Danica',    'middle_name' => 'Cruz',      'last_name' => 'Aquino',    'birth_date' => '2006-06-19', 'gender' => 'female', 'address' => '66 Victoria St., Manila',      'contact_number' => '09201110060', 'guardian_name' => 'Marites Aquino',    'guardian_contact' => '09201110160', 'guardian_relationship' => 'Mother'],
        ];

        foreach ($students as $index => $data) {
            // student001, student002, ... student060
            $email = 'student' . str_pad($index + 1, 3, '0', STR_PAD_LEFT) . '@school.edu.ph';
            $user  = User::where('email', $email)->first();

            // Grade level determines year of student number: 7→2019, 8→2018, ..., 12→2014
            $gradeIndex  = (int) floor($index / 10); // 0–5 for grades 7–12
            $entryYear   = 2019 - $gradeIndex;
            $sequenceNum = str_pad($index + 1, 4, '0', STR_PAD_LEFT);

            Student::create([
                'user_id'                => $user?->id,
                'student_number'         => $entryYear . '-' . $sequenceNum,
                'first_name'             => $data['first_name'],
                'middle_name'            => $data['middle_name'],
                'last_name'              => $data['last_name'],
                'birth_date'             => $data['birth_date'],
                'gender'                 => $data['gender'],
                'address'                => $data['address'],
                'contact_number'         => $data['contact_number'],
                'guardian_name'          => $data['guardian_name'],
                'guardian_contact'       => $data['guardian_contact'],
                'guardian_relationship'  => $data['guardian_relationship'],
                'status'                 => 'active',
            ]);
        }
    }
}
