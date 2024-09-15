<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['roleName' => 'admin'],
            ['roleName' => 'user'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
