<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Carbon\Carbon;
class BlogController extends Controller
{
    /**
     * Lấy danh sách 5 blog gần đây nhất
     */
    public function getLatestBlogs()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->take(5)->get();
        return response()->json([
            'status' => 'success',
            'blogs' => $blogs
        ], 200);
    }

    /**
     * Lấy danh sách blog có thời gian sau 7 ngày kể từ thời điểm đăng
     */
    public function getOlderBlogs()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        $olderBlogs = Blog::where('created_at', '<', $sevenDaysAgo)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'blogs' => $olderBlogs
        ], 200);
    }
    public function getBlogDetails($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'blog' => $blog
        ], 200);
    }

}
