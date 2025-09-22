<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'name' => 'Edriane O. Bangonon',
            'email' => 'edriane@gmail.com',
            'role' => 'user',
            'password' => password_hash('edriane1109', PASSWORD_DEFAULT)
            ],
            [
            'name' => 'Juan Two Three',
            'email' => 'Jua@gmail.com',
            'role' => 'user',
            'password' => password_hash('kobe1109', PASSWORD_DEFAULT)
            ],
            [
            'name' => 'Max Verstappen',
            'email' => 'maxVerstappen@gmail.com',
            'role' => 'admin',
            'password' => password_hash('max1109', PASSWORD_DEFAULT)
            ],
            
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
