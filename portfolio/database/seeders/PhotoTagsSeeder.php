<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photoTags = [];

        // Giả sử mỗi photo sẽ có từ 1 đến 3 tag
        for ($photo_id = 1; $photo_id <= 200; $photo_id++) {
            // Random số lượng tag cho mỗi photo (1 đến 3 tags)
            $tagCount = rand(1, 3);

            // Random tag_id từ 1 đến 15 cho mỗi photo
            $tags = collect(range(1, 15))->random($tagCount);

            foreach ($tags as $tag_id) {
                $photoTags[] = [
                    'photo_id' => $photo_id,
                    'tag_id' => $tag_id,
                ];
            }
        }

        // Chèn tất cả các bản ghi vào bảng 'photo_tags'
        DB::table('photo_tags')->insert($photoTags);
    }
}
