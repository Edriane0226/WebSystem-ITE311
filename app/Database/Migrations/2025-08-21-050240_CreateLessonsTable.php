<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'lessonID' => [
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
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'startDate' => [
                'type' => 'DATE',
                'null' => true
            ],
            'endDate' => [
                'type' => 'DATE',
                'null' => true
            ],
        ]);
        $this->forge->addKey('lessonID');
        $this->forge->addForeignKey('courseID', 'courses', 'courseID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lessons');
    }

    public function down()
    {
        $this->forge->dropTable('lessons');
    }
}
