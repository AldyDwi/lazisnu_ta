<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'superadmin',
                'name'     => 'SuperAdmin',
                'email'    => 'ardiansyahaldy123@gmail.com',
                'password' => password_hash('123456789', PASSWORD_DEFAULT),
                'role'     => 'superadmin',
                'phone'    => '081234567890',
                'gender' => 'male',
                'is_active' => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Simple Query
        $this->db->table('users')->insertBatch($data);
    }
}
