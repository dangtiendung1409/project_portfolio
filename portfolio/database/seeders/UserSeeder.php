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
                'username' => 'admin',
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => 'https://example.com/images/jane_doe.jpg',
                'location' => 'Hanoi,VietNam',
                'cover_photo' => 'https://example.com/images/john_doe.jpg',
                'bio' => 'Lover of nature and animals.',
                'created_at' => now(),
                'role_id' => 1,
            ],
            [
                'username' => 'user1',
                'name' => 'user1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => 'https://example.com/images/john_doe.jpg',
                'location' => 'Hanoi,VietNam',
                'cover_photo' => 'https://example.com/images/john_doe.jpg',
                'bio' => 'Photographer and traveler.',
                'created_at' => now(),
                'role_id' => 2,
            ],
            [
                'username' => 'user2',
                'name' => 'user2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('12345'),
                'profile_picture' => 'https://example.com/images/john_doe.jpg',
                'location' => 'Hanoi,VietNam',
                'cover_photo' => 'https://example.com/images/john_doe.jpg',
                'bio' => 'Photographer and traveler.',
                'created_at' => now(),
                'role_id' => 2,
            ],
        ];

        DB::table('users')->insert($users);
    }
}
