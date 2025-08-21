<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
            'firstName' => '',
            'lastName' => 'Bangonon',
            'middleName' => 'Ortiz',
            'email' => 'edriane@gmail.com',
            'role' => 'student',
            'password' => password_hash('edriane1109', PASSWORD_DEFAULT)
            ],
            [
            'firstName' => 'Kobe',
            'lastName' => 'Bryan',
            'middleName' => 'Bean',
            'email' => 'kobe123@gmail.com',
            'role' => 'instructor',
            'password' => password_hash('kobe1109', PASSWORD_DEFAULT)
            ],
            [
            'firstName' => 'Max',
            'lastName' => 'Verstappem',
            'middleName' => 'Emillian',
            'email' => 'maxVerstappen@gmail.com',
            'role' => 'admin',
            'password' => password_hash('max1109', PASSWORD_DEFAULT)
            ],
            
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
