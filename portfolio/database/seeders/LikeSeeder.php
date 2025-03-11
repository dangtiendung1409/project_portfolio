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

        //  Chỉ lấy ảnh có privacy_status = 0
        $photoIds = DB::table('photos')
            ->where('privacy_status', 0)
            ->pluck('id')
            ->toArray();

        //  Chỉ lấy gallery có visibility = 0
        $galleryIds = DB::table('galleries')
            ->where('visibility', 0)
            ->pluck('id')
            ->toArray();

        $notifications = [];

        // Chỉ 30% ảnh sẽ có like
        $photoIdsWithLikes = $faker->randomElements($photoIds, round(count($photoIds) * 0.3));

        foreach ($photoIdsWithLikes as $photoId) {
            $numLikes = rand(10, 40);
            $likedUsers = $faker->randomElements($userIds, $numLikes);

            foreach ($likedUsers as $userId) {
                $likeId = DB::table('likes')->insertGetId([
                    'user_id' => $userId,
                    'photo_id' => $photoId,
                    'gallery_id' => null,
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
                        'content' => "{$users[$userId]} liked your photo.",
                        'is_read' => 0,
                        'notification_date' => now(),
                    ];
                }
            }
        }

        // Chọn ngẫu nhiên 50 gallery có like (hoặc tất cả nếu ít hơn 50)
        $galleryIdsWithLikes = $faker->randomElements($galleryIds, min(50, count($galleryIds)));


        foreach ($galleryIdsWithLikes as $galleryId) {
            $numLikes = rand(10, 40);
            $likedUsers = $faker->randomElements($userIds, $numLikes);

            foreach ($likedUsers as $userId) {
                $likeId = DB::table('likes')->insertGetId([
                    'user_id' => $userId,
                    'photo_id' => null,
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
                        'content' => "{$users[$userId]} liked your gallery.",
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
