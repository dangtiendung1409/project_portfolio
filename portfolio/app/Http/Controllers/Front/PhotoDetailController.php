<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoDetailController extends Controller
{
    public function getPhotoDetail(Request $request, $token)
    {
        $photo = Photo::with(['user', 'category',])->where('photo_token', $token)->first();

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        return response()->json([
            'data' => $photo,
        ], 200);
    }
}
