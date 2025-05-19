<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistributionPageTable extends Migration
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
            'program_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_deleted' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Set Primary Key
        $this->forge->addKey('id', true);

        // Set Foreign Key ke tabel 'programs'
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE');

        // Buat tabel
        $this->forge->createTable('distribution_page');
    }

    public function down()
    {
        // Hapus tabel jika rollback
        $this->forge->dropTable('distribution_page');
    }
}
