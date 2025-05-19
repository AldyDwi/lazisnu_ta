<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['male', 'female'],
                'null'       => false,
                'after'      => 'region_id'
            ],
        ]);

    }

    public function down()
    {
        $this->forge->dropColumn('users', ['gender']);
    }
}
