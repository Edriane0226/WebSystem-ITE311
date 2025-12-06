<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTimeTable extends Migration
{
    public function up()
    {
        $this->forge->addfield(
            [
                'timeID' => [
                    'type' => 'INT',
                    'constraint' => 10,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'timeSlot' => [
                    'type' => 'VARCHAR',
                    'constraint' => 25,
                    'null' => false,
                ]
            ]
        );
        $this->forge->addKey('timeID');
        $this->forge->createTable('time');
    }

    public function down()
    {
        $this->forge->dropTable('time');
    }
}
