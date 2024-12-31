<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PhotoImages;
use Illuminate\Http\Request;

class PhotoDetailController extends Controller
{
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
