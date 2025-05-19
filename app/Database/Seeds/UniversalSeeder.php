<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UniversalSeeder extends Seeder
{
    public function run()
    {
        // Seed provience table
        $this->db->table('provience')->insertBatch([
            [
                'name' => 'Jawa Timur',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Jawa Jawa Jawa',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Garam Dan Mengkudu',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}