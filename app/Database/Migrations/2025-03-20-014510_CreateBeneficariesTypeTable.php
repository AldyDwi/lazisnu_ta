<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeneficariesTypeTable extends Migration
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
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('beneficaries_type');
    }

    public function down()
    {
        $this->forge->dropTable('beneficaries_type');
    }
}
