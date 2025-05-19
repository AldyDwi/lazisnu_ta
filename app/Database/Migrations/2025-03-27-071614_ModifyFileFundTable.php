<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyFileFundTable extends Migration
{
    public function up()
    {
        // Drop existing table if needed (optional)
        $this->forge->dropTable('file_fund', true);

        // Create new structure
        $this->forge->addField([
            'id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned'   => true,
                'auto_increment' => true
            ],
            'distribution_page_id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned'   => true,
            ],
            'distribution_image_id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN', 
                'default' => false
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('distribution_page_id', 'distribution_page', 'id', 'CASCADE');
        $this->forge->addForeignKey('distribution_image_id', 'distribution_image', 'id', 'CASCADE');
        $this->forge->createTable('file_fund');
    }

    public function down()
    {
        $this->forge->dropTable('file_fund');
    }
}
