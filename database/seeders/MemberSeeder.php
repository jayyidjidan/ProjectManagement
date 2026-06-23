<?php

namespace Database\Seeders;

use App\Models\Users;
use App\Models\Members;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $activeStatus = DB::table('status_members')
            ->where('status_name', 'Active')
            ->value('id_status');

        $members = [

            [
                'username' => 'rizky',
                'email' => 'rizky@example.com',
                'member_name' => 'Rizky Pratama',
                'id_position' => 1,
            ],

            [
                'username' => 'budi',
                'email' => 'budi@example.com',
                'member_name' => 'Budi Santoso',
                'id_position' => 2,
            ],

            [
                'username' => 'sinta',
                'email' => 'sinta@example.com',
                'member_name' => 'Sinta Maharani',
                'id_position' => 3,
            ],

            [
                'username' => 'dimas',
                'email' => 'dimas@example.com',
                'member_name' => 'Dimas Nugraha',
                'id_position' => 7,
            ],

            [
                'username' => 'putri',
                'email' => 'putri@example.com',
                'member_name' => 'Putri Lestari',
                'id_position' => 9,
            ],

        ];

        foreach ($members as $data) {

            $user = Users::firstOrCreate(
                [
                    'email' => $data['email']
                ],
                [
                    'username' => $data['username'],
                    'password' => Hash::make('password'),
                    'id_role' => 2,
                ]
            );

            Members::firstOrCreate(
                [
                    'id_user' => $user->id_user
                ],
                [
                    'member_name' => $data['member_name'],
                    'id_position' => $data['id_position'],
                    'joined_date' => now(),
                    'id_status' => $activeStatus,
                    'work_location' => 'onsite',
                ]
            );
        }
    }
}