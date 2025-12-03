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
            'role' => 3,
            'password' => password_hash('12345678', PASSWORD_DEFAULT)
            ],
            [
            'name' => 'Juan Two Three',
            'email' => 'Jua@gmail.com',
            'role' => 2,
            'password' => password_hash('12345678', PASSWORD_DEFAULT)
            ],
            [
            'name' => 'Max Verstappen',
            'email' => 'maxVerstappen@gmail.com',
            'role' => 1,
            'password' => password_hash('12345678', PASSWORD_DEFAULT)
            ],
            
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
