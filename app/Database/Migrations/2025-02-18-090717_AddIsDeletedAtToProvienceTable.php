<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsDeletedAtToProvienceTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('provience', [
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);
    }

    public function down()
    {
        //
    }
}
