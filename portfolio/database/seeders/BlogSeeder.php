<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Blog;
use Carbon\Carbon;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titles = [
            "The Future of Photography",
            "Exploring Nature Through a Lens",
            "10 Tips for Better Portraits",
            "Capturing the Perfect Sunset",
            "How to Master Street Photography",
            "The Art of Black and White Photography",
            "Understanding Camera Settings",
            "A Guide to Landscape Photography",
            "Photography Gear You Must Have",
            "Editing Techniques for Stunning Photos",
            "Shooting in Low Light Conditions",
            "The Magic of Golden Hour",
            "Composition Techniques Every Photographer Should Know",
            "How to Tell a Story Through Photos",
            "Travel Photography Tips for Beginners"
        ];

        // Lấy 5 blog mới nhất (thời gian hiện tại)
        foreach (array_slice($titles, 0, 5) as $title) {
            Blog::create([
                'author_id'   => 1,
                'title'       => $title,
                'slug'        => Str::slug($title) . '-' . time(),
                'content'     => fake()->paragraphs(rand(5, 10), true),
                'cover_image' => 'images/covers/covers_' . rand(0, 19) . '.jpeg',
                'images'      => 'images/photos/image-' . rand(1, 200) . '.jpeg',
                'created_at'  => Carbon::now(), // Thời gian hiện tại
                'updated_at'  => Carbon::now(),
            ]);
        }

        // Lấy 10 blog cũ hơn (7 ngày trước)
        foreach (array_slice($titles, 5, 10) as $title) {
            Blog::create([
                'author_id'   => 1,
                'title'       => $title,
                'slug'        => Str::slug($title) . '-' . time(),
                'content'     => fake()->paragraphs(rand(5, 10), true),
                'cover_image' => 'images/covers/covers_' . rand(0, 19) . '.jpeg',
                'images'      => 'images/photos/image-' . rand(1, 200) . '.jpeg',
                'created_at'  => Carbon::now()->subDays(7), // 7 ngày trước
                'updated_at'  => Carbon::now()->subDays(7),
            ]);
        }
    }
}
