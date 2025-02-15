<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Photo;

class CategoryController extends Controller
{
    /**
     * Lấy tất cả các ảnh thuộc về một danh mục dựa trên tag của danh mục.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPhotosByCategorySlugs(Request $request)
    {
        // Lấy danh sách các slug từ query parameters
        $slugs = explode(',', $request->query('slugs'));

        // Tìm các danh mục dựa trên slug
        $categories = Category::whereIn('slug', $slugs)->get();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'Categories not found'], 404);
        }

        // Lấy tất cả các ảnh thuộc về các danh mục đó + thông tin user + tags
        $photos = Photo::whereIn('category_id', $categories->pluck('id'))
            ->with(['user', 'tags'])
            ->get();

        return response()->json($photos);
    }

}
