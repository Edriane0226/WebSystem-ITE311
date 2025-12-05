<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentStatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['statusName' => 'Enrolled'],
            ['statusName' => 'Completed'],
            ['statusName' => 'Dropped'],
        ];
        $this->db->table('enrollmentstatus')->insertBatch($data);
    }
}
