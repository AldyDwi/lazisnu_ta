<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToTransactions extends Migration
{
    public function up()
    {
        // Menambahkan kolom status ke tabel transactions
        $this->forge->addColumn('transactions', [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['open', 'close'],
                'default' => 'open',
                'after' => 'updated_at', // Menentukan posisi kolom baru
            ],
        ]);
    }

    public function down()
    {
        // Menghapus kolom status dari tabel transactions
        $this->forge->dropColumn('transactions', 'status');
    }
}