<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
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
    public function getGalleriesByUserName($username)
    {
        $user = User::where('username', $username)->first();

        if ($user) {
            $galleries = Gallery::where('user_id', $user->id)
                ->where('visibility', 0)
                ->with(['photo', 'user']) // Thêm 'user' để lấy thông tin user
                ->get();

            return response()->json($galleries);
        } else {
            return response()->json(['error' => 'User not found'], 404);
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
