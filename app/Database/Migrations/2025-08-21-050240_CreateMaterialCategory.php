<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialCategory extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'categoryID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'categoryName' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);
        $this->forge->addKey('categoryID');
        $this->forge->createTable('materialCategories');
    }

    public function down()
    {
        $this->forge->dropTable('materialCategories');
    }
}
