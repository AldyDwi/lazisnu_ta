<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeneficariesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['orphan', 'student', 'boarding_student'],
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('beneficaries');
    }

    public function down()
    {
        $this->forge->dropTable('beneficaries');
    }
}