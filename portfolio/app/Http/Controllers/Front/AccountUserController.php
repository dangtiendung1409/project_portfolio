<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notification;
use App\Models\PhotoImages;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AccountUserController extends Controller
{
    public function getLikedPhotos(Request $request)
    {
        // Lấy user_id từ token đã xác thực
        $user = Auth::user();
        $likedPhotos = Like::where('user_id', $user->id)
            ->with([
                'photoImage.photo.user',
            ])
            ->orderBy('like_date', 'desc')
            ->get();

        return response()->json([
            'data' => $likedPhotos
        ]);
    }
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'sometimes|string|nullable',
            'profile_picture' => 'sometimes|file|mimes:jpeg,png|max:1024', // Chỉ file JPEG/PNG < 1MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cập nhật thông tin user chỉ khi có thay đổi
        $updated = false;

        if ($request->filled('username') && $request->username !== $user->username) {
            $user->username = $request->username;
            $updated = true;
        }

        if ($request->filled('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $updated = true;
        }

        if ($request->filled('bio') && $request->bio !== $user->bio) {
            $user->bio = $request->bio;
            $updated = true;
        }

        // Xử lý upload ảnh vào public/images/avatars
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Lưu ảnh vào public/images/avatars
            $file->move(public_path('images/avatars'), $filename);

            // Xóa ảnh cũ nếu có
            if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture));
            }

            // Cập nhật đường dẫn ảnh vào database
            $user->profile_picture = 'images/avatars/' . $filename;
            $updated = true;
        }

        // Chỉ save khi có thay đổi
        if ($updated) {
            $user->save();
            return response()->json([
                'message' => 'Profile updated successfully!',
                'user' => $user
            ], 200);
        }

        return response()->json(['message' => 'No changes made.'], 200);
    }
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:5|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        // Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }
}
