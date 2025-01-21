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
use Illuminate\Support\Str;


class AccountUserController extends Controller
{
    // like photo
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
    // gallery
    public function getAllGalleries(Request $request)
    {
        // Lấy thông tin người dùng từ token xác thực
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lấy gallery của user đang đăng nhập
        $galleries = Gallery::with(['photoImages.photo' , 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $galleries,
            'message' => 'Galleries fetched successfully!',
        ], 200);
    }
    public function getGalleryDetails($galleries_code)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tìm gallery theo galleries_code
        $gallery = Gallery::with(['photoImages.photo.user', 'user'])
            ->where('galleries_code', $galleries_code)
            ->first();

        // Kiểm tra nếu không tìm thấy gallery
        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found or access denied'], 404);
        }

        return response()->json([
            'data' => $gallery,
            'message' => 'Gallery details fetched successfully!',
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
        $gallery->galleries_code = (string) Str::uuid();
        $gallery->save();

        return response()->json([
            'message' => 'Gallery created successfully!',
            'gallery' => $gallery
        ], 201);
    }
    public function updateGallery(Request $request, $galleries_code)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tìm gallery theo galleries_code
        $gallery = Gallery::where('galleries_code', $galleries_code)->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found or access denied'], 404);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'sometimes|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cập nhật thông tin gallery
        if ($request->filled('title') && $request->title !== $gallery->galleries_name) {
            $gallery->galleries_name = $request->title;
        }

        if ($request->filled('description') && $request->description !== $gallery->galleries_description) {
            $gallery->galleries_description = $request->description;
        }

        if ($request->filled('visibility')) {
            $gallery->visibility = $request->visibility;
        }

        // Lưu lại các thay đổi
        $gallery->save();

        return response()->json([
            'message' => 'Gallery updated successfully!',
            'gallery' => $gallery
        ], 200);
    }
    public function deleteGallery(Request $request, $galleries_code)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tìm gallery theo galleries_code
        $gallery = Gallery::where('galleries_code', $galleries_code)->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found or access denied'], 404);
        }

        if ($gallery->user_id !== $user->id) {
            return response()->json(['message' => 'You do not have permission to delete this gallery'], 403);
        }

        // Xóa gallery (trigger event `deleting`)
        $gallery->delete();

        return response()->json([
            'message' => 'Gallery deleted successfully!',
        ], 200);
    }

    // profile
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

    //change password
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
