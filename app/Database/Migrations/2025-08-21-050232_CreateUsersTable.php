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
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true
            ],
            // 'firstName' => [
            //     'type' => 'VARCHAR',
            //     'constraint' => 25,
            //     'null' => false
            // ],
            // 'lastname' => [
            //     'type' => 'VARCHAR',
            //     'constraint' => 25,
            //     'null' => false
            // ],
            // 'middleName' => [    
            //     'type' => 'VARCHAR',
            //     'constraint' => 25,
            //     'null' => false
            // ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],  
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'role' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
            ]);

            $this->forge->addKey('userID');
            $this->forge->addForeignKey('role', 'roles', 'roleID', 'CASCADE', 'CASCADE');
            $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
