<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CoursesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'courseCode' => 'CS101',
                'courseTitle' => 'Computer Science Basics',
                'courseDescription' => 'An introduction to computer science concepts.',
                'schoolYearID' => 1,
                'teacherID' => 2,
                'statusID' => 1
            ],
            [
                'courseCode' => 'WD201',
                'courseTitle' => 'Web Development',
                'courseDescription' => 'Building websites using HTML, CSS, and JavaScript.',
                'schoolYearID' => 1,
                'teacherID' => 2,
                'statusID' => 1
            ],
            [
                'courseCode' => 'DB301',
                'courseTitle' => 'Database Management',
                'courseDescription' => 'Introduction to SQL and database design principles.',
                'schoolYearID' => 2,
                'teacherID' => 2,
                'statusID' => 1
            ],
            [
                'courseCode' => 'DSA401',
                'courseTitle' => 'Data Structures and Algorithms',
                'courseDescription' => 'Understanding data structures and algorithmic techniques.',
                'schoolYearID' => 2,
                'teacherID' => 2,
                'statusID' => 1
            ],
            [
                'courseCode' => 'AI501',
                'courseTitle' => 'Introduction to Artificial Intelligence',
                'courseDescription' => 'Basics of AI, machine learning, and neural networks.',
                'schoolYearID' => 3,
                'teacherID' => 2,
                'statusID' => 1
            ]
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
