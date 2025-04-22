<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $adminPassword = password_hash(env('SEEDER_ADMIN_PASSWORD'), PASSWORD_DEFAULT);
        $userPassword  = password_hash(env('SEEDER_USER_PASSWORD'), PASSWORD_DEFAULT);


        $data = [
            [
                'name'     => env('SEEDER_ADMIN_NAME'),
                'email'    => env('SEEDER_ADMIN_EMAIL'),
                'password' => $adminPassword,
                'role'     => env('SEEDER_ADMIN_ROLE'),
            ],
            [
                'name'     => env('SEEDER_USER_NAME'),
                'email'    => env('SEEDER_USER_EMAIL'),
                'password' => $adminPassword,
                'role'     => env('SEEDER_USER_ROLE'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
