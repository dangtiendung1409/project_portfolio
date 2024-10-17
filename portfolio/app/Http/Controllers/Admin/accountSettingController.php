<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class accountSettingController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');
        return view('admin.AccountSettings.profile', compact('user', 'successMessage', 'errorMessage'));
    }

    public function updateProfile(Request $request)
    {
        // Xác thực dữ liệu
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'bio' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->bio = $validatedData['bio'];

        if ($request->hasFile('profile_picture')) {
            // Xóa ảnh cũ nếu có
            if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture));
            }

            $imageName = time() . '_' . uniqid() . '.' . $request->profile_picture->extension();
            $request->profile_picture->move(public_path('images/avatars'), $imageName);
            $user->profile_picture = 'images/avatars/' . $imageName;
        }

        // Lưu thay đổi
        $user->save();

        // Thông báo thành công
        Session::flash('successMessage', 'Profile updated successfully!');
        return redirect()->route('admin.profile');
    }


    public function changePassword(){

        $successMessage = Session::get('successMessage');
        $errorMessage = Session::get('errorMessage');

        return view('admin/AccountSettings/changePassword',compact('successMessage','errorMessage'));
    }
    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'password_current' => 'required|string',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($validatedData['password_current'], $user->password)) {
            return redirect()->back()->withErrors(['password_current' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($validatedData['password']);
        $user->save();

        Session::flash('successMessage', 'Password updated successfully!');
        return redirect()->route('admin.changePassword');
    }
}

