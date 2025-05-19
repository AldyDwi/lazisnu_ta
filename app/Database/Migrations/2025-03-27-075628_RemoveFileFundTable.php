<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveFileFundTable extends Migration
{
    public function up()
    {
        // Hapus tabel file_fund jika ada
        $this->forge->dropTable('file_fund', true);
    }

    public function down()
    {
        // Buat ulang tabel file_fund jika rollback
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'distribution_page_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'distribution_image_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'is_deleted' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
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
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('file_fund');
    }
}
