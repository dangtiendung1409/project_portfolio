<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class userController extends Controller
{
    // Phương thức hiển thị danh sách người dùng với phân trang
    public function index(Request $request)
    {
        $users = User::where('is_active', 1)->paginate(10);

        // Trả về view với danh sách người dùng
        return view('admin/User.user', compact('users'));
    }
    public function usersInActive(Request $request){
        $users = User::where('is_active', 0)->paginate(10);

        // Trả về view với danh sách người dùng
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
}
