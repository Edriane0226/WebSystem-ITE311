<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentStatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['statusName' => 'Pending'],
            ['statusName' => 'Enrolled'],
            ['statusName' => 'Completed'],
            ['statusName' => 'Dropped'],
        ];
    }
}
