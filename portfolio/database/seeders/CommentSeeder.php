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
        $users = DB::table('users')->pluck('name', 'id')->toArray(); // Lấy user_id => user_name
        $photoIds = DB::table('photos')->pluck('id')->toArray();

        // Chỉ chọn khoảng 30% số ảnh để có comment
        $photoIdsWithComments = $faker->randomElements($photoIds, (int) (count($photoIds) * 0.3));

        $notifications = [];

        foreach ($photoIdsWithComments as $photoId) {
            $numComments = rand(20, 50); // Mỗi ảnh có từ 20 - 50 comment
            $commentingUsers = $faker->randomElements($userIds, $numComments);

            foreach ($commentingUsers as $userId) {
                $commentId = DB::table('comments')->insertGetId([
                    'photo_id' => $photoId,
                    'user_id' => $userId,
                    'comment_text' => $faker->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Lấy user sở hữu ảnh
                $photoOwnerId = DB::table('photos')->where('id', $photoId)->value('user_id');

                // Gửi thông báo nếu người comment không phải là chủ ảnh
                if ($photoOwnerId && $photoOwnerId !== $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'recipient_id' => $photoOwnerId,
                        'photo_id' => $photoId,
                        'comment_id' => $commentId, // Chỉ lưu comment_id, không cần photo_id
                        'type' => 1, // Type 1 = Comment
                        'content' => "{$users[$userId]} commented on your photo", // Truyền tên user vào nội dung thông báo
                        'is_read' => 0,
                        'notification_date' => now(),
                    ];
                }
            }
        }

        // Chèn thông báo vào bảng 'notifications' nếu có
        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }
    }
}
