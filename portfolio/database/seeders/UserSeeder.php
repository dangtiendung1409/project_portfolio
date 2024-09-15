<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => 'https://example.com/images/john_doe.jpg',
                'bio' => 'Photographer and traveler.',
                'join_date' => now(),
                'role_id' => 2,
            ],
            [
                'username' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => 'https://example.com/images/john_doe.jpg',
                'bio' => 'Photographer and traveler.',
                'join_date' => now(),
                'role_id' => 2,
            ],
            [
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => 'https://example.com/images/jane_doe.jpg',
                'bio' => 'Lover of nature and animals.',
                'join_date' => now(),
                'role_id' => 1,
            ],
        ];

        // Insert users into the users table
        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
