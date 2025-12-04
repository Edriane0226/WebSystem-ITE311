<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnrollmentStatusTable extends Migration
{
    public function up()
    {
        $this->forge->addfield([
            'statusID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'statusName' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
        ]);

        $this->forge->addKey('statusID');
        $this->forge->createTable('enrollmentStatus');
    }

    public function down()
    {
        $this->forge->dropTable('enrollmentStatus');
    }
}
