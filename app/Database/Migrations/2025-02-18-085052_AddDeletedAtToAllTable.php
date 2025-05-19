<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToAllTable extends Migration
{
    public function up()
    {
        // $this->forge->addColumn('provience', [
        //     'deleted_at' => [
        //         'type' => 'DATETIME',
        //         'null' => true,
        //         'after' => 'updated_at',
        //     ],
        //     'is_deleted' => [
        //         'type' => 'BOOLEAN',
        //         'default' => false,
        //         'after' => 'deleted_at',
        //     ],
        // ]);

        
        $this->forge->addColumn('cities', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('districts', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('branches', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('region', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('rws', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('citizens', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('program_allocation', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('detail_fund', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('file_fund', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        $this->forge->addColumn('beneficaries', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        // Add columns to users table
        $this->forge->addColumn('users', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        // Add columns to transactions table
        $this->forge->addColumn('transactions', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        // Add columns to programs table
        $this->forge->addColumn('programs', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at',
            ],
            'is_deleted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'deleted_at',
            ],
        ]);

        // Add columns to more tables as needed
        // Repeat the above pattern for each table
    }

    public function down()
    {

        // $this->forge->dropColumn('provience', ['deleted_at', 'is_deleted']);

        // Drop columns from cities table
        $this->forge->dropColumn('cities', ['deleted_at', 'is_deleted']);

        // Drop columns from district table
        $this->forge->dropColumn('district', ['deleted_at', 'is_deleted']);

        // Drop columns from branches table
        $this->forge->dropColumn('branches', ['deleted_at', 'is_deleted']);

        // Drop columns from region table
        $this->forge->dropColumn('region', ['deleted_at', 'is_deleted']);

        // Drop columns from rws table
        $this->forge->dropColumn('rws', ['deleted_at', 'is_deleted']);

        // Drop columns from citizens table
        $this->forge->dropColumn('citizens', ['deleted_at', 'is_deleted']);

        // Drop columns from program_allocation table
        $this->forge->dropColumn('program_allocation', ['deleted_at', 'is_deleted']);

        // Drop columns from detail_fund table
        $this->forge->dropColumn('detail_fund', ['deleted_at', 'is_deleted']);

        // Drop columns from file_fund table
        $this->forge->dropColumn('file_fund', ['deleted_at', 'is_deleted']);

        // Drop columns from beneficaries table
        $this->forge->dropColumn('beneficaries', ['deleted_at', 'is_deleted']);

        // Drop columns from users table
        $this->forge->dropColumn('users', ['deleted_at', 'is_deleted']);

        // Drop columns from transactions table
        $this->forge->dropColumn('transactions', ['deleted_at', 'is_deleted']);

        // Drop columns from programs table
        $this->forge->dropColumn('programs', ['deleted_at', 'is_deleted']);

        // Drop columns from more tables as needed
        // Repeat the above pattern for each table
    }
}