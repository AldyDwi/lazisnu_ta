<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePhoneNumberFromCitizens extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('citizens', 'phone_number');
    }

    public function down()
    {
        $this->forge->addColumn('citizens', [
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'after' => 'name'
            ]
        ]);
    }
}
