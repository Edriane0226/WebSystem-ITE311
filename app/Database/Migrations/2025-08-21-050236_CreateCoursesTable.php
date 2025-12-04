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
            'courseCode' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'courseTitle' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'courseDescription' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'schoolYearID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'teacherID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true
            ],
            'statusID' => [
                'type' => 'INT',
                'constraint' => 20,
                'unsigned' => true
            ],
            // 'offers' => [
            //     'type' => 'INT',
            //     'constraint' => 10,
            //     'null' => true
            // ],
        ]);
        $this->forge->addKey('courseID');
        $this->forge->addForeignKey('teacherID', 'users', 'userID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('schoolYearID', 'schoolYear', 'schoolYearID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('statusID', 'courseStatus', 'statusID', 'CASCADE', 'CASCADE');
        // Imma add this later
        //$this->forge->addForeignKey('offers', 'offerings', 'offersID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courses');
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
