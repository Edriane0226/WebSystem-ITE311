<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'categoryName' => 'Modules',
            ],
            [
                'categoryName' => 'Assignments',
            ],
        ];

        $this->db->table('materialCategories')->insertBatch($data);
    }
}
