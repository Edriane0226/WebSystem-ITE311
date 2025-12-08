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
            [
                'categoryName' => 'Submissions',
            ],
        ];

        $this->db->table('materialCategories')->insertBatch($data);
    }
}
