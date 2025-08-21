<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this -> forge -> addField([
            'userID' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'firstName' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => false
            ],
            'lastname' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => false
            ],
            'middleName' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => false
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['student', 'instructor', 'admin'],
                'default' => 'student'
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ]  
            ]);

            $this->forge->addKey('userID');
            $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
