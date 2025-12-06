<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseOfferings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'offeringID' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'courseID' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'schoolYearID' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'startDate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'endDate' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'Schedule' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
        ]);

        $this->forge->addKey('offeringID');
        $this->forge->addForeignKey('courseID', 'courses', 'courseID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('schoolYearID', 'schoolYear', 'schoolYearID', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('Schedule', 'time', 'timeID', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courseOfferings');
    }

    public function down()
    {
        $this->forge->dropTable('courseOfferings');
    }
}
