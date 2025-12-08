<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAutoCloseToAssignments extends Migration
{
    public function up()
    {
        $fields = [
            'autoClose' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'isClosed',
            ],
        ];

        $this->forge->addColumn('assignments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('assignments', 'autoClose');
    }
}
