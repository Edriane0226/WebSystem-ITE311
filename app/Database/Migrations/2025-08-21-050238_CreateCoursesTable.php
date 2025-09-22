<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'courseID' => [
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'courseTitle' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'teacherID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
        ]);
        $this->forge->addKey('courseID');
        $this->forge->addForeignKey('instructorID', 'users', 'userID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courses');
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
