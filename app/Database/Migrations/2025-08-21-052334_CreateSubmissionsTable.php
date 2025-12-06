<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'submissionID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'userID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'AssignmentID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'materialID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'submissionDate' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('submissionID');
        $this->forge->addForeignKey('userID', 'users', 'userID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('AssignmentID', 'assignments', 'AssignmentID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('materialID', 'materials', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('submissions');
    }

    public function down()
    {
        $this->forge->dropTable('submissions');
    }
}
