<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Mathematics 101',
                'description' => 'Basic concepts of algebra, geometry, and calculus.',
                'date_published' => '2023-01-15'
            ],
            [
                'name' => 'Introduction to Programming',
                'description' => 'Fundamentals of programming using Python.',
                'date_published' => '2023-02-20'
            ],
            [
                'name' => 'Web Development',
                'description' => 'Building websites using HTML, CSS, and JavaScript.',
                'date_published' => '2023-03-10'
            ],
            [
                'name' => 'Database Management',
                'description' => 'Introduction to SQL and database design principles.',
                'date_published' => '2023-04-05'
            ],
            [
                'name' => 'Data Structures and Algorithms',
                'description' => 'Understanding data structures and algorithmic techniques.',
                'date_published' => '2023-05-12'
            ]
        ];
    }
}
