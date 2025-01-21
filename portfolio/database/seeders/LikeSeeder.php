<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();


        $userIds = DB::table('users')->pluck('id')->toArray();
        $photoIds = DB::table('photos')->pluck('id')->toArray();

        // Chèn 100 like ngẫu nhiên
        for ($i = 0; $i < 100; $i++) {
            DB::table('likes')->insert([
                'user_id' => $faker->randomElement($userIds),
                'photo_id' => $faker->randomElement($photoIds),
                'like_date' => now(),
            ]);
        }
    }
}
