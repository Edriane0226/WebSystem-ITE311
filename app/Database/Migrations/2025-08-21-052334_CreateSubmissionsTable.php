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
            'quizID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'answer' => [
                'type' => 'TEXT'
            ],
            'score' => [
                'type' => 'INT',
                'constraint' => 100,
                'null' => true
            ],
            'submissionDate' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('submissionID');
        $this->forge->addForeignKey('userID', 'users', 'userID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('quizID', 'quizzes', 'quizID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('submissions');
    }

    public function down()
    {
        $this->forge->dropTable('submissions');
    }
}
