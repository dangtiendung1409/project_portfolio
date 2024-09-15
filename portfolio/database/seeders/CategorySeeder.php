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
            'Urban ExplorationWedding','Countryside','Other'

        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'category_name' => $category,
            ]);
        }
    }
}
