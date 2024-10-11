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
        $query = User::query();

        // Lọc theo username
        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->input('username') . '%');
        }

        // Lọc theo email
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        // Lọc theo join_date (từ ngày nào đến ngày nào)
        if ($request->filled('start_date')) {
            $query->whereDate('join_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('join_date', '<=', $request->input('end_date'));
        }

        // Lọc theo số lần vi phạm (violation_count)
        if ($request->filled('violation_count')) {
            $query->where('violation_count', '>=', $request->input('violation_count'));
        }

        // Lấy danh sách người dùng đã active
        $users = $query->where('is_active', 1)
            ->withCount('photos')
            ->paginate(10)
            ->appends($request->all());

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
