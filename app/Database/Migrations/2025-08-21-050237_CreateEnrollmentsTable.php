<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'enrollmentID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'enrollment_date' => [
                'type' => 'DATE',
                'null' => true
            ],
        ]);

        $this->forge->addKey('enrollmentID');
        $this->forge->addForeignKey('user_id', 'users', 'userID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'courseID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('enrollments');
    }

    public function down()
    {
        $this->forge->dropTable('enrollments');
    }
}
