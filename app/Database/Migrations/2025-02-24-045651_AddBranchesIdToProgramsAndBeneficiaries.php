<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBranchesIdToProgramsAndBeneficiaries extends Migration
{
    public function up()
    {
        // Menambahkan kolom branches_id ke tabel programs
        $this->forge->addColumn('programs', [
            'branches_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id', // Menentukan posisi kolom baru
            ],
        ]);

        // Menambahkan kolom branches_id ke tabel beneficiaries
        $this->forge->addColumn('beneficaries', [
            'branches_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id', // Ganti dengan kolom yang ada di tabel beneficiaries
            ],
        ]);
    }

    public function down()
    {
        // Menghapus kolom branches_id dari tabel programs
        $this->forge->dropColumn('programs', 'branches_id');

        // Menghapus kolom branches_id dari tabel beneficiaries
        $this->forge->dropColumn('beneficiaries', 'branches_id');
    }
}