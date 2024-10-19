<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PhotoImages;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function getImages()
    {
        $images = PhotoImages::with('photo.category')->get();
        return response()->json($images);
    }
}
