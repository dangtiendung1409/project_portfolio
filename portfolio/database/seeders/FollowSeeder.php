<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $follows = [
            ['follower_id' => 1, 'following_id' => 2, 'follow_date' => now()],
            ['follower_id' => 2, 'following_id' => 1, 'follow_date' => now()],
        ];

        foreach ($follows as $follow) {
            DB::table('follows')->insert($follow);
        }
    }
}
