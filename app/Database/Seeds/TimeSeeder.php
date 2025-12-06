<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TimeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'timeSlot' => '08:00 - 09:00'
            ],
            [
                'timeSlot' => '09:00 - 10:00'
            ],
            [
                'timeSlot' => '10:00 - 11:00'
            ],
            [
                'timeSlot' => '11:00 - 12:00'
            ],
            [
                'timeSlot' => '13:00 - 14:00'
            ],
            [
                'timeSlot' => '14:00 - 15:00'
            ],
            [
                'timeSlot' => '15:00 - 16:00'
            ],
            [
                'timeSlot' => '16:00 - 17:00'
            ],
        ];

        $this->db->table('time')->insertBatch($data);
    }
}
