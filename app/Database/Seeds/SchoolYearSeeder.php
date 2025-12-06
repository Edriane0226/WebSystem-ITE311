<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'schoolYear' => '2025-2026',
            'Semester' => 1,
            ],
            [
            'schoolYear' => '2025-2026',
            'Semester' => 2,
            ],
            [
            'schoolYear' => '2025-2026',
            'Semester' => 3,
            ],
            [
            'schoolYear' => '2026-2027',
            'Semester' => 1,
            ],
            [
            'schoolYear' => '2026-2027',
            'Semester' => 2,
            ],
            [
            'schoolYear' => '2026-2027',
            'Semester' => 3,
            ],            
        ];

        $this->db->table('schoolYear')->insertBatch($data);
    }
}
