<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Abstract', 'Aerial', 'Black and White',
            'Boudoir', 'Animals', 'Celebrities',
            'City and Architecture', 'Commercial', 'Concert',
            'Family', 'Fashion', 'Film',
            'Fine Art', 'Food', 'Journalism',
            'Landscapes', 'Macro', 'Nature',
            'Night', 'People','Performing Arts',
            'Sport', 'Still Life', 'Street',
            'Transportation','Travel','Underwater',
            'Urban Exploration','Wedding','Countryside','Other'
        ];

        foreach ($categories as $index => $category) {
            $randomImageNumber = rand(1, 200); // Lấy số ngẫu nhiên từ 1 đến 200
            $imagePath = '/images/photos/image-' . $randomImageNumber . '.jpeg'; // Đường dẫn ảnh

            DB::table('categories')->insert([
                'category_name' => $category,
                'slug' => strtolower(str_replace(' ', '-', $category)), // Tạo slug từ category_name
                'image' => $imagePath, // Gán đường dẫn ảnh
            ]);
        }
    }
}
