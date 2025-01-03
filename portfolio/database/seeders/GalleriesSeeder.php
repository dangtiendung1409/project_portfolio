<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'galleries_name' => 'Urban Exploration',
                'galleries_description' => 'Photos capturing the essence of urban life.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($galleries as $gallery) {
            DB::table('galleries')->insert($gallery);
        }
    }
}
