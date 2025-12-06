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
            'Semester' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ]
        ]);
        $this->forge->addKey('schoolYearID');
        $this->forge->addForeignKey('Semester', 'semester', 'semesterID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('schoolYear');
    }

    public function down()
    {
        $this->forge->dropTable('schoolYear');
    }
}
