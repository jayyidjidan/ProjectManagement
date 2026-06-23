<?php

namespace Database\Seeders;

use App\Models\Users;
use App\Models\Members;
use App\Models\Roles;
use App\Models\Jabatan;
use App\Models\StatusMembers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectManagerSeeder extends Seeder
{
    public function run(): void
    {
        $role = Roles::firstOrCreate([
            'role_name' => 'Project Manager'
        ]);

        $position = Jabatan::firstOrCreate([
            'position_name' => 'Project Manager'
        ]);

        $status = StatusMembers::firstOrCreate([
            'status_name' => 'Active'
        ]);

        $user = Users::firstOrCreate(
            [
                'email' => 'pm@plainthing.com'
            ],
            [
                'username' => 'projectmanager',
                'password' => Hash::make('password123'),
                'id_role' => $role->id_role,
            ]
        );

        Members::firstOrCreate(
            [
                'id_user' => $user->id_user
            ],
            [
                'member_name' => 'Project Manager',
                'id_position' => $position->id_position,
                'joined_date' => now(),
                'id_status' => $status->id_status,
                'work_location' => 'onsite',
            ]
        );
    }
}