<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyTransactionsTable extends Migration
{
    public function up()
    {
        // Mengubah kolom-kolom menjadi nullable
        $fields = [
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['donations', 'fund_distribution', 'field_officer_commision'],
                'null' => true,
                'default' => null,
            ],
            'citizen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'rw_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'default' => null,
            ],
            'debit' => [
                'type' => 'DOUBLE',
                'null' => true,
                'default' => null,
            ],
            'credit' => [
                'type' => 'DOUBLE',
                'null' => true,
                'default' => null,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'total_collected' => [
                'type' => 'DOUBLE',
                'null' => true,
                'default' => null,
            ],
        ];

        $this->forge->modifyColumn('transactions', $fields);
    }

    public function down()
    {
        // Mengembalikan kolom-kolom menjadi tidak nullable
        $fields = [
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['donations', 'fund_distribution', 'field_officer_commision'],
                'null' => false,
            ],
            'citizen_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'rw_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'program_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'debit' => [
                'type' => 'DOUBLE',
                'null' => false,
            ],
            'credit' => [
                'type' => 'DOUBLE',
                'null' => false,
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'total_collected' => [
                'type' => 'DOUBLE',
                'null' => false,
            ],
        ];

        $this->forge->modifyColumn('transactions', $fields);
    }
}