<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseStatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'statusName' => 'active',
            ],
            [
            'statusName' => 'inactive',
            ],
        ];

        $this->db->table('coursestatus')->insertBatch($data);
    }
}
