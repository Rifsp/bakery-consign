<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();

        $users = [
            [
                'username'  => 'admin',
                'email'     => 'admin@bakery.com',
                'password'  => password_hash('admin123', PASSWORD_DEFAULT),
                'nama'      => 'Administrator',
                'role'      => 'admin',
                'telepon'   => '081234567890',
                'foto'      => null,
                'is_aktif'  => true,
            ],
            [
                'username'  => 'sales',
                'email'     => 'sales@bakery.com',
                'password'  => password_hash('sales123', PASSWORD_DEFAULT),
                'nama'      => 'Sales Person',
                'role'      => 'sales',
                'telepon'   => '081234567891',
                'foto'      => null,
                'is_aktif'  => true,
            ],
        ];

        foreach ($users as $user) {
            $userModel->insert($user);
        }

        echo "UserSeeder executed: 2 users created.\n";
    }
}