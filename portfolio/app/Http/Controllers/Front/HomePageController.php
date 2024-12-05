<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PhotoImages;
use App\Models\User;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function getImages()
    {
        $images = PhotoImages::with(['photo.category', 'photo.user'])->get();
        return response()->json($images);
    }

    public function getFollows() {
        // Lấy tất cả users cùng với 4 ảnh đầu tiên từ tất cả các photo của họ
        $follows = User::with(['photos.images' => function ($query) {
            $query->limit(4); // Giới hạn chỉ lấy 4 ảnh đầu tiên từ tất cả các photo
        }])->get();

        // Khai báo biến chứa kết quả cuối cùng
        $result = $follows->map(function ($user) {
            // Lấy ra tất cả ảnh từ các photo của user
            $allImages = $user->photos->flatMap(function ($photo) {
                return $photo->images; // Lấy tất cả ảnh từ từng photo
            });

            // Lấy ra 4 ảnh đầu tiên từ tất cả ảnh của user
            $user->images = $allImages->take(4); // Lấy 4 ảnh đầu tiên
            return $user; // Trả về user với 4 ảnh
        });

        return response()->json($result);
    }
    public function getPhotoDetail(Request $request, $token)
    {
        $photoImage = PhotoImages::with([
            'photo.user',
            'photo.category',
        ])->where('photo_token', $token)->first();

        if (!$photoImage) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        return response()->json([
            'data' => $photoImage,
        ], 200);
    }

}
