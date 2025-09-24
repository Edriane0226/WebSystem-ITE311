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
            'studentID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'courseID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'enrolledAt' => [
                'type' => 'DATE',
                'null' => true
            ],
        ]);

        $this->forge->addKey('enrollmentID');
        $this->forge->addForeignKey('studentID', 'users', 'userID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('courseID', 'courses', 'courseID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('enrollments');
    }

    public function down()
    {
        $this->forge->dropTable('enrollments');
    }
}
