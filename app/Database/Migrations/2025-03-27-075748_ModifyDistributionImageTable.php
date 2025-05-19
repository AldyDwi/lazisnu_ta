<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyDistributionImageTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('distribution_image', [
            'distribution_page_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id', 
            ],
            'CONSTRAINT distribution_image_distribution_page_fk FOREIGN KEY (distribution_page_id) REFERENCES distribution_page(id) ON UPDATE CASCADE'
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('distribution_image', 'distribution_page_id');
    }
}
