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
        $notifications = [
            ['user_id' => 1, 'notification_type' => 'Like', 'content' => 'Your photo has been liked!', 'is_read' => false, 'notification_date' => now()],
            ['user_id' => 2, 'notification_type' => 'Comment', 'content' => 'Your photo received a new comment.', 'is_read' => false, 'notification_date' => now()],
        ];

        foreach ($notifications as $notification) {
            DB::table('notifications')->insert($notification);
        }
    }
}
