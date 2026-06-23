<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        Users::updateOrCreate(

            [
                'username' => 'superadmin'
            ],

            [
                'email'    => 'superadmin@plainthing.com',
                'password' => Hash::make('password123'),
                'id_role'  => 1,
            ]

        );
    }
}