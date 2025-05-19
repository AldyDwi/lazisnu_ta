<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBranchesTable extends Migration
{
    public function up()
    {
        // Drop columns city_id, province_id, and district_id
        $this->forge->dropColumn('branches', ['city_id', 'province_id', 'district_id']);

        // Add column region_id
        $fields = [
            'region_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id' // Position the new column after 'id'
            ],
        ];
        $this->forge->addColumn('branches', $fields);
    }

    public function down()
    {
        // Add columns city_id, province_id, and district_id back
        $fields = [
            'city_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'id'
            ],
            'district_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'city_id'
            ],
            'province_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'after' => 'district_id'
            ],
        ];
        $this->forge->addColumn('branches', $fields);

        // Drop column region_id
        $this->forge->dropColumn('branches', 'region_id');
    }
}