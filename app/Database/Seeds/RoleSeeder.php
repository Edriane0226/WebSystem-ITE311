<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'role_name' => 'admin',
            ],
            [
            'role_name' => 'teacher',
            ],
            [
            'role_name' => 'student',
            ],
            
        ];

        $this->db->table('roles')->insertBatch($data);
    }
}
