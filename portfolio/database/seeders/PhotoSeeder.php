<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hàm để tạo title ngẫu nhiên (1 đến 3 chữ)
        function generateRandomTitle()
        {
            $words = ['Sunset', 'Mountains', 'Skyline', 'City', 'Night', 'Ocean', 'Forest', 'Adventure', 'Beauty', 'Dream', 'Vision', 'Mystery', 'Harmony', 'Journey', 'Reflection'];
            $randomWords = collect($words)->random(rand(1, 3))->implode(' ');
            return $randomWords;
        }

        // Hàm để tạo description ngẫu nhiên
        function generateRandomDescription()
        {
            $descriptions = [
                'A breathtaking view of nature.',
                'An awe-inspiring scene of the outdoors.',
                'Captured at the perfect moment.',
                'A beautiful scene filled with vibrant colors.',
                'A moment of serenity and peace.',
                'An adventure in the wilderness.',
                'The beauty of a bustling city.',
                'An iconic landscape that speaks to the soul.'
            ];
            return collect($descriptions)->random();
        }

        // Hàm để tạo location ngẫu nhiên
        function generateRandomLocation()
        {
            $locations = ['Mountain Range', 'Downtown', 'Beachside', 'Countryside', 'Riverside', 'National Park', 'Desert', 'Rainforest'];
            return collect($locations)->random();
        }

        $privacyStatus = ['public', 'private'];
        $photos = [];
        $photoImages = [];

        // Danh sách tên ảnh có sẵn
        $imageNames = [];
        for ($k = 1; $k <= 200; $k++) {
            $imageNames[] = '/images/photos/image-' . $k . '.jpeg';
        }

        for ($i = 1; $i <= 200; $i++) {
            // Tạo dữ liệu cho bảng 'photos'
            $photos[] = [
                'title' => generateRandomTitle(),
                'description' => generateRandomDescription(),
                'upload_date' => now(),
                'location' => generateRandomLocation(),
                'user_id' => rand(1, 3),
                'category_id' => rand(1, 30),
                'privacy_status' => collect($privacyStatus)->random(),  // Random Public hoặc Private
            ];

            // Tạo từ 1 đến 3 hình ảnh cho mỗi 'photo' trong bảng 'photo_images'
            $numberOfImages = rand(1, 3); // Số hình ảnh ngẫu nhiên cho mỗi photo
            $usedImages = []; // Mảng lưu trữ các hình ảnh đã sử dụng để tránh trùng lặp

            for ($j = 0; $j < $numberOfImages; $j++) {
                // Chọn ngẫu nhiên một tên ảnh chưa được sử dụng
                do {
                    $randomImage = $imageNames[array_rand($imageNames)];
                } while (in_array($randomImage, $usedImages));

                $usedImages[] = $randomImage; // Thêm vào danh sách đã sử dụng

                $photoImages[] = [
                    'photo_id' => $i,
                    'photo_status' => 'approved',
                    'image_url' => $randomImage,
                    'photo_token' => (string) Str::uuid(),
                ];
            }
        }

        // Chèn dữ liệu vào bảng 'photos'
        DB::table('photos')->insert($photos);

        // Chèn dữ liệu vào bảng 'photo_images'
        DB::table('photo_images')->insert($photoImages);
    }
}
