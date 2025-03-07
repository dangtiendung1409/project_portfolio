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
        $users = DB::table('users')->pluck('name', 'id')->toArray(); // Lấy user_id => user_name
        $photoIds = DB::table('photos')->pluck('id')->toArray();
        $galleryIds = DB::table('galleries')->pluck('id')->toArray();

        $notifications = [];

        // Chỉ 30% ảnh sẽ có like
        $photoIdsWithLikes = $faker->randomElements($photoIds, round(count($photoIds) * 0.3));

        // Duyệt qua từng photo được chọn để có like
        foreach ($photoIdsWithLikes as $photoId) {
            $numLikes = rand(10, 40); // Giảm số like trong khoảng 10-40
            $likedUsers = $faker->randomElements($userIds, $numLikes);

            foreach ($likedUsers as $userId) {
                $likeId = DB::table('likes')->insertGetId([
                    'user_id' => $userId,
                    'photo_id' => $photoId,
                    'gallery_id' => null, // Chỉ like photo thì gallery_id = null
                    'like_date' => now(),
                ]);

                // Lấy user sở hữu ảnh
                $photoOwnerId = DB::table('photos')->where('id', $photoId)->value('user_id');
                if ($photoOwnerId && $photoOwnerId !== $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'recipient_id' => $photoOwnerId,
                        'like_id' => $likeId,
                        'photo_id' => $photoId,
                        'type' => 0, // Like photo
                        'content' => "{$users[$userId]} liked your photo.", // Truyền tên user
                        'is_read' => 0,
                        'notification_date' => now(),
                    ];
                }
            }
        }

        // Chỉ 30% gallery sẽ có like
        $galleryIdsWithLikes = $faker->randomElements($galleryIds, round(count($galleryIds) * 0.3));

        // Duyệt qua từng gallery được chọn để có like
        foreach ($galleryIdsWithLikes as $galleryId) {
            $numLikes = rand(10, 40); // Giảm số like trong khoảng 10-40
            $likedUsers = $faker->randomElements($userIds, $numLikes);

            foreach ($likedUsers as $userId) {
                $likeId = DB::table('likes')->insertGetId([
                    'user_id' => $userId,
                    'photo_id' => null, // Chỉ like gallery thì photo_id = null
                    'gallery_id' => $galleryId,
                    'like_date' => now(),
                ]);

                // Lấy user sở hữu gallery
                $galleryOwnerId = DB::table('galleries')->where('id', $galleryId)->value('user_id');
                if ($galleryOwnerId && $galleryOwnerId !== $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'recipient_id' => $galleryOwnerId,
                        'like_id' => $likeId,
                        'photo_id' => null,
                        'type' => 3, // Like gallery
                        'content' => "{$users[$userId]} liked your gallery.", // Truyền tên user
                        'is_read' => 0,
                        'notification_date' => now(),
                    ];
                }
            }
        }

        // Chèn tất cả thông báo vào DB
        if (!empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }
    }
}
