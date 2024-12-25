<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comments = [
            ['photo_image_id' => 1, 'user_id' => 2, 'comment_text' => 'Amazing photo!', 'created_at' => now(),'updated_at' => now()],
            ['photo_image_id' => 2, 'user_id' => 1, 'comment_text' => 'Great shot!', 'created_at' => now(),'updated_at' => now()],
        ];

        foreach ($comments as $comment) {
            DB::table('comments')->insert($comment);
        }
    }
}
