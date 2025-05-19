<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlugAndDateToDistributionPage extends Migration
{
    public function up()
    {
        $this->forge->addColumn('distribution_page', [
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'after'      => 'program_id',
            ],
            'date' => [
                'type'  => 'DATE',
                'null'  => false,
                'after' => 'slug',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('distribution_page', ['slug', 'date']);
    }
}
