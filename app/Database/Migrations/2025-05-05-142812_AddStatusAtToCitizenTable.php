<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAtToCitizenTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('citizens', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['already', 'not yet'],
                'default'    => 'not yet',
                'null'       => false,
                'after'      => 'address',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('citizens', ['status']);
    }
}
