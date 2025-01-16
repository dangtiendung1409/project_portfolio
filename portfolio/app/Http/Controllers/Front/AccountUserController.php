<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
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
    public function getAllGalleries(Request $request)
    {
        // Lấy thông tin người dùng từ token xác thực
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lấy gallery của user đang đăng nhập
        $galleries = Gallery::with(['photoImages.photo.user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $galleries,
            'message' => 'Galleries fetched successfully!',
        ], 200);
    }
    public function addGallery(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tạo gallery mới
        $gallery = new Gallery();
        $gallery->galleries_name = $request->title;
        $gallery->galleries_description = $request->description;
        $gallery->visibility = $request->visibility;
        $gallery->user_id = $user->id;
        $gallery->save();

        return response()->json([
            'message' => 'Gallery created successfully!',
            'gallery' => $gallery
        ], 201);
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
