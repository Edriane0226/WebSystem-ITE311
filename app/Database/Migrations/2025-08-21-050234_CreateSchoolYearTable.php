<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolYearTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'schoolYearID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'schoolYear' => [
                'type' => 'VARCHAR',
                'constraint' => 9,
            ],
        ]);
        $this->forge->addKey('schoolYearID');
        $this->forge->createTable('schoolYear');
    }

    public function down()
    {
        $this->forge->dropTable('schoolYear');
    }
}
