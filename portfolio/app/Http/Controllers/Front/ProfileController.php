<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;

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
}
