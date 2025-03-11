<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            PhotoSeeder::class,
            TagSeeder::class,
            PhotoTagsSeeder::class,
            GalleriesSeeder::class,
            LikeSeeder::class,
            CommentSeeder::class,
            FollowSeeder::class,
            ReportSeeder::class,
        ]);
    }
}
