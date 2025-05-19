<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneNumberToCitizens extends Migration
{
    public function up()
    {
        $this->forge->addColumn('citizens', [
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'after' => 'name'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('citizens', 'phone_number');
    }
}
