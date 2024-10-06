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
            ['photo_image_id' => 1, 'user_id' => 2,'comment_status'=>'pending', 'comment_text' => 'Amazing photo!', 'comment_date' => now()],
            ['photo_image_id' => 2, 'user_id' => 1,'comment_status'=>'pending', 'comment_text' => 'Great shot!', 'comment_date' => now()],
        ];

        foreach ($comments as $comment) {
            DB::table('comments')->insert($comment);
        }
    }
}
