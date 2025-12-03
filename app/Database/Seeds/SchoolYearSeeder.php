<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SchoolYearSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'schoolYear' => '2023-2024',
            ],
            [
            'schoolYear' => '2024-2025',
            ],
            [
            'schoolYear' => '2025-2026',
            ],
            [
            'schoolYear' => '2026-2027',
            ],
            [
            'schoolYear' => '2027-2028',
            ],
            [
            'schoolYear' => '2028-2029',
            ],
            [
            'schoolYear' => '2029-2030',
            ]
            
        ];

        $this->db->table('schoolYear')->insertBatch($data);
    }
}
