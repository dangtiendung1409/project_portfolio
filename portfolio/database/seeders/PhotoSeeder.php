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
            return collect($words)->random(rand(1, 3))->implode(' ');
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

        // Danh sách trạng thái privacy
        $privacyStatus = [0, 1]; // 1: private, 0: public

        // Danh sách hình ảnh ngẫu nhiên
        $imageNames = [];
        for ($k = 1; $k <= 200; $k++) {
            $imageNames[] = '/images/photos/image-' . $k . '.jpeg';
        }

        // Tạo dữ liệu cho bảng 'photos'
        $photos = [];
        for ($i = 1; $i <= 300; $i++) {
            $photos[] = [
                'title' => generateRandomTitle(),
                'description' => generateRandomDescription(),
                'upload_date' => now(),
                'location' => generateRandomLocation(),
                'privacy_status' => collect($privacyStatus)->random(), // 0 hoặc 1
                'photo_status' => 'approved', // Mặc định trạng thái
                'image_url' => $imageNames[array_rand($imageNames)], // Chọn ngẫu nhiên ảnh
                'photo_token' => (string) Str::uuid(), // Tạo UUID ngẫu nhiên
                'user_id' => rand(1, 100),
                'category_id' => rand(1, 30),
                'total_views' => rand(50, 150),
            ];
        }

        // Chèn dữ liệu vào bảng 'photos'
        DB::table('photos')->insert($photos);
    }
}
