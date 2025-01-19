<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GalleriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'galleries_name' => 'Nature Photography',
                'galleries_description' => 'A collection of beautiful nature photos.',
                'user_id' => 1,
                'visibility' => 1,
                'galleries_code' => (string) Str::uuid(), // Thêm galleries_code ở đây
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'galleries_name' => 'Urban Exploration',
                'galleries_description' => 'Photos capturing the essence of urban life.',
                'user_id' => 2,
                'visibility' => 0,
                'galleries_code' => (string) Str::uuid(), // Thêm galleries_code ở đây
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('galleries')->insert($galleries); // Chèn tất cả vào cùng một lần
    }
}
