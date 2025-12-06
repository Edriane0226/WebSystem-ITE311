<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSemesterTable extends Migration
{
    public function up()
    {
        $this->forge->addfield(
            [
                'semesterID' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'semesterName' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ]
            ]
        );
        $this->forge->addKey('semesterID');
        $this->forge->createTable('semester');
    }

    public function down()
    {
        $this->forge->dropTable('semester');
    }
}
