<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CourseStatus extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'statusID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'statusName' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ]
        ]);
        $this->forge->addKey('statusID');
        $this->forge->createTable('courseStatus');
    }

    public function down()
    {
        $this->forge->dropTable('courseStatus');
    }
}
