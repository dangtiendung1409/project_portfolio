<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class userController extends Controller
{
    public function index(Request $request)
    {
        // Lấy danh sách người dùng active
        $users = User::where('is_active', 1)
            ->withCount('photos')
            ->paginate(10);
        return view('admin/User.user', compact('users'));
    }

    public function usersInActive(Request $request)
    {
        // Lấy danh sách người dùng inactive
        $users = User::where('is_active', 0)
            ->withCount('photos')
            ->paginate(10);
        return view('admin/User.InActiveUser', compact('users'));
    }
    public function unlockUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = 1;
            $user->save();
            Session::flash('successMessage', 'User unlocked successfully!');
        } catch (\Exception $e) {
            Session::flash('errorMessage', 'Error unlocking the user: ' . $e->getMessage());
        }

        return redirect()->back();  // Chuyển hướng về trang trước
    }
    public function getUserPhotos($id)
    {
        $photos = Photo::where('user_id', $id)
            ->whereHas('images', function ($query) {
                $query->where('photo_status', 'approved');
            })
            ->with(['images' => function ($query) {
                $query->where('photo_status', 'approved');
            }])
            ->paginate(20);

        $user = User::findOrFail($id);
        // Trả về view hiển thị ảnh
        return view('admin/User.userPhotos', compact('photos','user'));
    }
    public function getUserGalleries($id)
    {
        $galleries = Gallery::where("user_id",$id)->paginate(10);
        $user = User::findOrFail($id);
        // Trả về view hiển thị ảnh
        return view('admin/User.galleries',compact('galleries','user'));
    }
    public function getGalleryPhotos($id)
    {
        $gallery = Gallery::with('photoImages')->findOrFail($id);

        $photos = $gallery->photoImages()->paginate(10);

        return view('admin/User.galleryPhotos', compact('gallery', 'photos'));
    }

}
