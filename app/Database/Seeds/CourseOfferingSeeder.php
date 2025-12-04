<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseOfferingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'courseID' => 1,
                'schoolYearID' => 1,
                'startDate' => '2023-09-01',
                'endDate' => '2024-06-15',
            ],
            [
                'courseID' => 2,
                'schoolYearID' => 1,
                'startDate' => '2023-09-01',
                'endDate' => '2024-06-15',
            ],
            [
                'courseID' => 3,
                'schoolYearID' => 1,
                'startDate' => '2023-09-01',
                'endDate' => '2024-06-15',
            ],
        ];

        $this->db->table('courseOfferings')->insertBatch($data);
    }
}
