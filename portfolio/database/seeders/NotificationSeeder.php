<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // type 0 = like , type 1 = comment
        $notifications = [
            [
                'user_id' => 1,
                'like_id' => 1,
                'comment_id' => null,
                'photo_image_id' => 10,
                'type' => 0,
                'content' => 'Your photo has been liked!',
                'is_read' => false,
                'notification_date' => now(),
            ],
            [
                'user_id' => 2,
                'like_id' => null,
                'comment_id' => 1,
                'photo_image_id' => 15,
                'type' => 1,
                'content' => 'Your photo received a new comment.',
                'is_read' => false,
                'notification_date' => now(),
            ],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}
