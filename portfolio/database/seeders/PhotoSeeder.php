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

        // Tạo mảng privacy_status ngẫu nhiên
        $privacyStatus = ['public', 'private'];

        $photos = [];

        for ($i = 1; $i <= 200; $i++) {
            $photos[] = [
                'title' => generateRandomTitle(),
                'description' => generateRandomDescription(),
                'image_url' => '/images/image-' . $i . '.jpeg',
                'upload_date' => now(),
                'location' => generateRandomLocation(),
                'user_id' => rand(1, 3),  // Random từ 1 đến 3 cho user_id
                'category_id' => rand(1, 30),  // Random từ 1 đến 30 cho category_id
                'privacy_status' => collect($privacyStatus)->random(),  // Random Public hoặc Private
            ];
        }

        // Chèn tất cả các bản ghi vào bảng 'photos'
        DB::table('photos')->insert($photos);
    }
}
