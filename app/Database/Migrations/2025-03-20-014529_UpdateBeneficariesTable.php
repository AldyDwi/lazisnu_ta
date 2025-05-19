<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBeneficariesTable extends Migration
{
    public function up()
    {
        // Hapus kolom 'type'
        $this->forge->dropColumn('beneficaries', 'type');

        // Tambah kolom 'type_id'
        $this->forge->addColumn('beneficaries', [
            'type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'name',
            ],
            'CONSTRAINT FOREIGN KEY (type_id) REFERENCES beneficaries_type(id) ON UPDATE CASCADE',
        ]);
    }

    public function down()
    {
        // Hapus kolom 'type_id'
        $this->forge->dropColumn('beneficaries', 'type_id');

        // Tambah kembali kolom 'type'
        $this->forge->addColumn('beneficaries', [
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['orphan', 'student', 'boarding_student'],
            ],
        ]);
    }
}
