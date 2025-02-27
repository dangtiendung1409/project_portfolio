<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Photo;
use Tymon\JWTAuth\Facades\JWTAuth;
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

        // Khởi tạo query cho ảnh thuộc các danh mục đã tìm được
        $query = Photo::whereIn('category_id', $categories->pluck('id'));

        // Nếu có token trong header, lọc bỏ các ảnh của user bị block
        $token = $request->header('Authorization');
        if ($token) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    $blockedUserIds = $user->blockedUsers()->pluck('blocked_id');
                    $query->whereNotIn('user_id', $blockedUserIds);
                }
            } catch (\Exception $e) {
                // Nếu token không hợp lệ hoặc không xác thực được user thì bỏ qua bước lọc
            }
        }

        // Lấy tất cả các ảnh cùng với thông tin user và tags
        $photos = $query->with(['user', 'tags'])->get();

        return response()->json($photos);
    }

}
