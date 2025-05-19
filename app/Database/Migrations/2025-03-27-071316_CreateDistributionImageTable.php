<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistributionImageTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
            ],
            'is_deleted' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
        ]);

        // Set Primary Key
        $this->forge->addKey('id', true);

        // Buat tabel
        $this->forge->createTable('distribution_image');
    }

    public function down()
    {
        // Hapus tabel jika rollback
        $this->forge->dropTable('distribution_image');
    }
}
