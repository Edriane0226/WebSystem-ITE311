<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'semesterName' => '1st Semester'
            ],
            [
                'semesterName' => '2nd Semester'
            ],
            [
                'semesterName' => 'Summer'
            ],         
        ];

        $this->db->table('semester')->insertBatch($data);
    }
}
