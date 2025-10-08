<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'courseTitle' => 'Mathematics 101',
                'courseDescription' => 'Basic concepts of algebra, geometry, and calculus.',
                'teacherID' => 2
            ],
            [
                'courseTitle' => 'Introduction to Programming',
                'courseDescription' => 'Fundamentals of programming using Python.',
                'teacherID' => 2
            ],
            [
                'courseTitle' => 'Web Development',
                'courseDescription' => 'Building websites using HTML, CSS, and JavaScript.',
                'teacherID' => 2
            ],
            [
                'courseTitle' => 'Database Management',
                'courseDescription' => 'Introduction to SQL and database design principles.',
                'teacherID' => 2
            ],
            [
                'courseTitle' => 'Data Structures and Algorithms',
                'courseDescription' => 'Understanding data structures and algorithmic techniques.',
                'teacherID' => 2
            ]
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
