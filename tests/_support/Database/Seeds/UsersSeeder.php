<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
	public function run()
    {
        $builder = $this->db->table('users');

        $users = [
            [
                'user_id' => '37e46a3d-bd18-440e-a473-daa6ea059b75',
                'full_name' => 'Reza Sariful Fikri',
                'username' => 'reza',
                'level' => 'admin',
                'password' => password_hash('reza', PASSWORD_DEFAULT),
                'created_at' => '2021-07-03 09:31:56',
                'updated_at' => '2021-07-03 09:31:56'
            ],
            [
                'user_id' => '920d2c5a-cf6b-4d74-8937-141b243f4141',
                'full_name' => 'Dian Pranata',
                'username' => 'dian',
                'level' => 'cashier',
                'password' => password_hash('dian', PASSWORD_DEFAULT),
                'created_at' => '2021-07-03 09:32:18',
                'updated_at' => '2021-07-03 09:32:18'
            ]
        ];

        foreach ($users as $user) {
            $builder->insert($user);
        }
	}
}
