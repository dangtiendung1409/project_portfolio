<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;

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

        $photos = $query->with(['user', 'tags'])->get();

        return response()->json($photos);
    }
}
