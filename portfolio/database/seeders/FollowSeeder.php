<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FollowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = DB::table('users')->select('id', 'name')->get(); // Lấy ID và tên user
        $userIds = $users->pluck('id')->toArray();
        $userNames = $users->pluck('name', 'id')->toArray(); // Map ID -> Name
        $totalUsers = count($userIds);

        // Chỉ chọn khoảng 30% user để có follow
        $usersWithFollow = $faker->randomElements($userIds, (int) ($totalUsers * 0.3));

        $follows = [];
        $notifications = [];

        foreach ($usersWithFollow as $followerId) {
            // Xác định số lượng follow ngẫu nhiên (20 - 70)
            $numFollows = rand(20, 70);

            // Lấy danh sách người được follow, loại bỏ chính mình
            $followingIds = array_diff($userIds, [$followerId]);
            shuffle($followingIds);
            $followingIds = array_slice($followingIds, 0, $numFollows);

            foreach ($followingIds as $followingId) {
                // Tránh follow trùng lặp
                $follows[] = [
                    'follower_id' => $followerId,
                    'following_id' => $followingId,
                    'follow_date' => now(),
                ];

                // Gửi thông báo follow với tên của user
                $notifications[] = [
                    'user_id' => $followerId, // Người follow
                    'recipient_id' => $followingId, // Người được follow
                    'type' => 2, // Type 2 = Follow
                    'content' => "{$userNames[$followerId]} has followed you.",
                    'is_read' => 0,
                    'notification_date' => now(),
                ];
            }
        }

        // Chèn dữ liệu vào bảng follows
        DB::table('follows')->insert($follows);

        // Chèn thông báo follow vào bảng notifications
        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }
    }
}
