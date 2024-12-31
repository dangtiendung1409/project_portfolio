<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();


        $userIds = DB::table('users')->pluck('id')->toArray();
        $photoIds = DB::table('photo_images')->pluck('id')->toArray();

        // Chèn 100 comment ngẫu nhiên
        for ($i = 0; $i < 100; $i++) {
            DB::table('comments')->insert([
                'photo_image_id' => $faker->randomElement($photoIds),
                'user_id' => $faker->randomElement($userIds),
                'comment_text' => $faker->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
