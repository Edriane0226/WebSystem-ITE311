<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'AssignmentID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'courseID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'materialID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'allowedAttempts' => [
                'type' => 'INT',
                'constraint' => 1,
                'null' => true
            ],
            'publishDate' => [
                'type' => 'DATE',
                'null' => true
            ],
            'dueDate' => [
                'type' => 'DATE',
                'null' => true
            ],
        ]);
        $this->forge->addKey('AssignmentID');
        $this->forge->addForeignKey('courseID', 'courses', 'courseID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('materialID', 'materials', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assignments');
    }

    public function down()
    {
        $this->forge->dropTable('assignments');
    }
}
