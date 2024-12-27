<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\PhotoImages;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    public function dashboard() {
        $approvedPhotosCount = PhotoImages::where('photo_status', 'approved')->count();
        $totalUser = User::count();
        $totalCategories = Category::count();

        // list photo pending
        $photoPending = Photo::whereHas('images', function($query) {
            $query->where('photo_status', 'pending');
        })->with(['images' => function ($query) {
            $query->where('photo_status', 'pending');
        }])->paginate(10);

        // list report pending
        $reports = Report::where('status', 'pending')->with(['reporter', 'violator', 'photoImage'])->paginate(10);

        // list comment pending
        $comments = Comment::with(['photoImage.photo', 'user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('admin/dashboard', compact(
            'approvedPhotosCount',
            'totalUser',
            'totalCategories',
            'photoPending',
            'comments',
            'reports'
        ));
    }
}

