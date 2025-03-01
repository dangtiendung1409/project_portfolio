<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Like;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{
    public function getUserByUserName($username)
    {
        $user = User::where('username', $username)->first();

        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
    public function getPhotosByUserName($username)
    {
        $user = User::where('username', $username)->first();

        if ($user) {
            $photos = Photo::where('user_id', $user->id)
                ->where('photo_status', 'approved')
                ->where('privacy_status', 0)
                ->get();

            return response()->json($photos);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
    public function getTotalLikesByUsername($username)
    {
        $user = \App\Models\User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Tính tổng số like của tất cả ảnh của user thỏa mãn điều kiện:
        // photo_status = 'approved' và privacy_status = 0
        $totalLikes = Like::whereHas('photo', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where('photo_status', 'approved')
                ->where('privacy_status', 0);
        })->count();

        return response()->json([
            'success' => true,
            'data' => [
                'username'    => $user->username,
                'total_likes' => $totalLikes,
            ]
        ]);
    }

    public function getGalleriesByUserName($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        try {
            // Lấy user hiện tại để kiểm tra danh sách người bị chặn
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');

            $galleries = Gallery::where('user_id', $user->id)
                ->where('visibility', 0)
                ->with([
                    'user',
                    'photo' => function ($q) use ($blockedUserIds) {
                        if ($blockedUserIds->isNotEmpty()) {
                            $q->whereNotIn('user_id', $blockedUserIds);
                        }
                    }
                ])
                ->get();

            return response()->json($galleries);
        } catch (\Exception $e) {
            // Nếu không có token hoặc xảy ra lỗi, trả về gallery mà không lọc các ảnh bị chặn
            $galleries = Gallery::where('user_id', $user->id)
                ->where('visibility', 0)
                ->with(['user', 'photo'])
                ->get();

            return response()->json($galleries);
        }
    }

    public function getGalleryDetailUser($galleries_code)
    {
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');

            $query = Gallery::with(['user', 'photo.user'])
                ->where('galleries_code', $galleries_code);

            // Lọc ảnh ngay trong truy vấn nếu có người bị chặn
            if ($blockedUserIds->isNotEmpty()) {
                $query->with(['photo' => function ($q) use ($blockedUserIds) {
                    $q->whereNotIn('user_id', $blockedUserIds);
                }]);
            }

            $gallery = $query->first();

            if (!$gallery) {
                return response()->json(['success' => false, 'message' => 'Gallery not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $gallery], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        } catch (\Exception $e) {
            $gallery = Gallery::with(['user', 'photo.user'])
                ->where('galleries_code', $galleries_code)
                ->first();

            if (!$gallery) {
                return response()->json(['success' => false, 'message' => 'Gallery not found'], 404);
            }

            return response()->json(['success' => true, 'data' => $gallery], 200);
        }
    }
}
