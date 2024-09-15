<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $likes = [
            ['user_id' => 1, 'photo_id' => 1, 'like_date' => now()],
            ['user_id' => 2, 'photo_id' => 2, 'like_date' => now()],
        ];

        foreach ($likes as $like) {
            DB::table('likes')->insert($like);
        }
    }
}
