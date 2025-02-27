<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchController extends Controller
{
    public function searchPhotos(Request $request)
    {
        $searchTerm = $request->get('q');
        $query = Photo::query();

        // Chỉ tìm kiếm ảnh có photo_status là 'approved' và privacy_status là 0
        $query->where('photo_status', 'approved')
            ->where('privacy_status', 0);

        if ($searchTerm) {
            // Tìm kiếm theo nhiều tiêu chí bao gồm cả tag
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('location', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('tags', function ($q) use ($searchTerm) {
                        $tags = explode(' ', $searchTerm);
                        $q->where(function ($query) use ($tags) {
                            foreach ($tags as $tag) {
                                $query->orWhere('tag_name', 'like', '%' . $tag . '%');
                            }
                        });
                    })
                    ->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('username', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Kiểm tra nếu có token trong header và lấy user từ token
        $token = $request->header('Authorization');
        if ($token) {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if ($user) {
                    $blockedUserIds = $user->blockedUsers()->pluck('blocked_id');
                    $query->whereNotIn('user_id', $blockedUserIds);
                }
            } catch (\Exception $e) {
                // Token không hợp lệ hoặc không có user
            }
        }

        $photos = $query->with(['user', 'tags'])->get();

        return response()->json($photos);
    }
}
