<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Horizontal', 'Technology', 'Internet',
            'One Person', 'Computer','Mid Adult Men',
            'Illustration','Business','Web Page',
            'Vector','Computer Monitor','Equipment',
            'Connection','Photography','Modern'
        ];

        foreach ($tags as $tag) {
            DB::table('tags')->insert([
                'tag_name' => $tag,
            ]);
        }
    }
}
