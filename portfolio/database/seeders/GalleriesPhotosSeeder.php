<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GalleriesPhotosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleriesPhotos = [
            ['galleries_id' => 1, 'photo_id' => 1],
            ['galleries_id' => 2, 'photo_id' => 2],
        ];

        foreach ($galleriesPhotos as $galleryPhoto) {
            DB::table('galleries_photos')->insert($galleryPhoto);
        }
    }
}
