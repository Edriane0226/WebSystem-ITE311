<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentAttempsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'attemptID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'submissionID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'attemptNumber' => [
                'type' => 'INT',
                'constraint' => 1,
            ],
            'attemptDate' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);
        $this->forge->addKey('attemptID');
        $this->forge->addForeignKey('submissionID', 'submissions', 'submissionID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assignmentAttempts');
    }

    public function down()
    {
        $this->forge->dropTable('assignmentAttempts');
    }
}
