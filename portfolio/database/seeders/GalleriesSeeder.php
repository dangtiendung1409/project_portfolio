<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GalleriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = DB::table('users')->pluck('id'); // Lấy danh sách user_id
        $photos = DB::table('photos')->pluck('id'); // Lấy danh sách photo_id
        // Danh sách tên bộ sưu tập ngẫu nhiên
        $galleryNames = [
            'Amazing Landscapes', 'Urban Exploration', 'Wildlife Wonders',
            'Street Photography', 'Black & White Art', 'Portrait Masterpieces',
            'Travel Diaries', 'Hidden Gems', 'Seasons of Life', 'Architectural Marvels'
        ];

        // Danh sách mô tả ngẫu nhiên
        $galleryDescriptions = [
            'A collection of breathtaking moments captured in time.',
            'Exploring the beauty of everyday life through the lens.',
            'A journey through nature’s most stunning landscapes.',
            'A tribute to the art of photography and storytelling.',
            'Capturing emotions, one frame at a time.',
            'An adventure into the world of colors and contrasts.',
            'Discovering unseen beauty in ordinary places.',
            'Celebrating life’s small yet significant moments.',
            'A mix of candid and artistic photography styles.',
            'Where imagination meets reality through the camera.'
        ];

        foreach ($users as $userId) {
            // Random số lượng gallery từ 3 đến 10 cho mỗi user
            $numGalleries = rand(3, 10);

            for ($i = 0; $i < $numGalleries; $i++) {
                $galleryId = DB::table('galleries')->insertGetId([
                    'galleries_name' => $galleryNames[array_rand($galleryNames)] . ' ' . Str::random(3),
                    'galleries_description' => $galleryDescriptions[array_rand($galleryDescriptions)],
                    'user_id' => $userId,
                    'visibility' => rand(0, 1), // 0: Private, 1: Public
                    'galleries_code' => Str::uuid(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Random từ 5 đến 10 ảnh cho mỗi gallery
                $numPhotos = rand(5, 10);
                $selectedPhotos = $photos->random($numPhotos);

                $galleryPhotos = [];
                foreach ($selectedPhotos as $photoId) {
                    $galleryPhotos[] = [
                        'galleries_id' => $galleryId,
                        'photo_id' => $photoId,
                    ];
                }

                // Chèn vào bảng galleries_photos
                DB::table('galleries_photos')->insert($galleryPhotos);
            }
        }
    }
}
