<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class AccountUserController extends Controller
{
    // my photo user
    public function getApprovedPhotos(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $approvedPhotos = Photo::where('user_id', $user->id)
            ->where('photo_status', 'approved')
            ->with(['category', 'tags'])
            ->get();

        return response()->json([
            'data' => $approvedPhotos,
            'message' => 'Approved photos fetched successfully!',
        ], 200);
    }
    public function deletePhoto(Request $request, $photo_id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tìm photo theo photo_id và user_id
        $photo = Photo::where('id', $photo_id)->where('user_id', $user->id)->first();

        if (!$photo) {
            return response()->json(['message' => 'Photo not found or access denied'], 404);
        }

        // Xóa các thông báo liên quan đến comments
        $comments = Comment::where('photo_id', $photo->id)->get();
        foreach ($comments as $comment) {
            Notification::where('comment_id', $comment->id)->delete();
        }

        // Xóa các thông báo liên quan đến likes
        $likes = Like::where('photo_id', $photo->id)->get();
        foreach ($likes as $like) {
            Notification::where('like_id', $like->id)->delete();
        }

        // Xóa tất cả các thông tin liên quan đến ảnh
        Notification::where('photo_id', $photo->id)->delete();
        Comment::where('photo_id', $photo->id)->delete();
        Report::where('photo_id', $photo->id)->delete();
        Like::where('photo_id', $photo->id)->delete();
        $photo->galleries()->detach(); // Xóa quan hệ với galleries
        $photo->tags()->detach(); // Xóa quan hệ với tags

        // Xóa ảnh
        $photo->delete();

        return response()->json([
            'message' => 'Photo and related information deleted successfully!',
        ], 200);
    }
    public function getPhoto($id)
    {
        $photo = Photo::with('tags', 'category')->findOrFail($id);
        $user = Auth::user();

        // Kiểm tra xem photo có thuộc về user không
        if ($photo->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($photo);
    }
    public function editPhoto(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'privacy_status' => 'nullable|string',
            'tags' => 'nullable|string', // Thêm tags vào validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $photo = Photo::findOrFail($id);
        $user = Auth::user();

        // Kiểm tra xem photo có thuộc về user không
        if ($photo->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Cập nhật các trường đã thay đổi
        if ($request->filled('title')) {
            $photo->title = $request->input('title');
        }
        if ($request->filled('description')) {
            $photo->description = $request->input('description');
        }
        if ($request->filled('location')) {
            $photo->location = $request->input('location');
        }
        if ($request->filled('category_id')) {
            $photo->category_id = $request->input('category_id');
        }
        if ($request->filled('privacy_status')) {
            $photo->privacy_status = $request->input('privacy_status');
        }

        // Lưu các thay đổi
        $photo->save();

        // **Xử lý tags**
        if ($request->filled('tags')) {
            $tags = explode(',', $request->input('tags')); // Lấy danh sách tag
            $tagIds = [];
            foreach ($tags as $tagName) {
                $tagName = trim($tagName); // Loại bỏ khoảng trắng

                // Kiểm tra tag đã tồn tại chưa
                $tag = Tag::firstOrCreate(['tag_name' => $tagName]);
                $tagIds[] = $tag->id;
            }

            // Cập nhật lại các tags
            $photo->tags()->sync($tagIds);
        }

        return response()->json(['message' => 'Photo updated successfully'], 200);
    }

    // lấy dữ liệu like photo
    public function getLikedPhotos(Request $request)
    {
        $user = Auth::user();

        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Lấy danh sách ID của những người bị chặn bởi người dùng hiện tại
        $blockedUserIds = Block::where('blocker_id', $user->id)
            ->pluck('blocked_id')
            ->toArray();

        // Lấy danh sách ảnh đã thích, có kiểm tra user bị chặn
        $likedPhotos = Like::where('user_id', $user->id)
            ->whereNotNull('photo_id')
            ->with(['photo.user'])
            ->orderBy('like_date', 'desc')
            ->get()
            ->filter(function ($like) use ($blockedUserIds) {
                return $like->photo && $like->photo->user && !in_array($like->photo->user->id, $blockedUserIds);
            });

        return response()->json([
            'data' => $likedPhotos
        ]);
    }

    // lấy dữ liệu like gallery
    public function getLikedGalleries(Request $request)
    {
        $user = Auth::user();

        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Lấy danh sách ID của những người bị chặn bởi người dùng hiện tại
        $blockedUserIds = Block::where('blocker_id', $user->id)
            ->pluck('blocked_id')
            ->toArray();

        // Lấy danh sách gallery đã thích, có kiểm tra user bị chặn
        $likedGalleries = Like::where('user_id', $user->id)
            ->whereNotNull('gallery_id')
            ->with(['gallery.photo', 'gallery.user'])
            ->orderBy('like_date', 'desc')
            ->get()
            ->filter(function ($like) use ($blockedUserIds) {
                return $like->gallery && $like->gallery->user && !in_array($like->gallery->user->id, $blockedUserIds);
            })
            ->values(); // Chuyển Collection thành mảng với chỉ số liên tục

        return response()->json([
            'data' => $likedGalleries
        ]);
    }


//     lấy dữ liệu những ảnh và gallery đã like
//    public function getLikedUser(Request $request)
//    {
//        $user = Auth::user();
//
//        // Kiểm tra nếu người dùng chưa đăng nhập
//        if (!$user) {
//            return response()->json(['message' => 'User not authenticated'], 401);
//        }
//
//        // Lấy danh sách ID của những người bị chặn bởi người dùng hiện tại
//        $blockedUserIds = Block::where('blocker_id', $user->id)
//            ->pluck('blocked_id')
//            ->toArray();
//
//        // Lấy tất cả lượt thích của người dùng hiện tại
//        $likedItems = Like::where('user_id', $user->id)
//            ->with([
//                'photo.user',
//                'gallery.user',
//                'gallery.photo'
//            ])
//            ->orderBy('like_date', 'desc')
//            ->get();
//
//        // Lọc các lượt thích để loại bỏ những lượt thích liên quan đến người bị chặn
//        $filteredLikedItems = $likedItems->filter(function ($like) use ($blockedUserIds) {
//            if ($like->photo_id && $like->photo && $like->photo->user) {
//                return !in_array($like->photo->user->id, $blockedUserIds);
//            }
//            if ($like->gallery_id && $like->gallery && $like->gallery->user) {
//                return !in_array($like->gallery->user->id, $blockedUserIds);
//            }
//            return false;
//        });
//
//        return response()->json(['data' => $filteredLikedItems], 200);
//    }
    public function deleteLike(Request $request, $like_id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tìm like dựa trên user_id và id của lượt thích
        $like = Like::where('user_id', $user->id)
            ->where('id', $like_id)
            ->first();

        if (!$like) {
            return response()->json(['message' => 'Like not found'], 404);
        }

        // Xóa các thông báo liên quan đến lượt thích này
        Notification::where('like_id', $like->id)->delete();

        // Xóa lượt thích
        $like->delete();

        // Xác định thông điệp dựa trên loại lượt thích
        $message = $like->photo_id ? 'Like on photo deleted successfully!' : 'Like on gallery deleted successfully!';

        return response()->json([
            'message' => $message,
        ], 200);
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
        $galleries = Gallery::with(['photo' , 'user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $galleries,
            'message' => 'Galleries fetched successfully!',
        ], 200);
    }
    public function addPhotoToGallery(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'gallery_id' => 'required|exists:galleries,id',
            'photo_id' => 'required|exists:photos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm gallery theo gallery_id
        $gallery = Gallery::where('id', $request->gallery_id)->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found or access denied'], 404);
        }

        // Kiểm tra nếu người dùng sở hữu gallery
        if ($gallery->user_id !== $user->id) {
            return response()->json(['message' => 'You do not have permission to modify this gallery'], 403);
        }

        // Kiểm tra nếu ảnh đã có trong gallery
        $photoExists = $gallery->photo()->where('photo_id', $request->photo_id)->exists();

        if ($photoExists) {
            // Xóa ảnh khỏi gallery
            $gallery->photo()->detach($request->photo_id);
            $message = 'Photo removed from gallery successfully!';
        } else {
            // Thêm ảnh vào gallery với created_at và updated_at
            $gallery->photo()->attach($request->photo_id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $message = 'Photo added to gallery successfully!';
        }

        return response()->json([
            'message' => $message,
            'gallery' => $gallery->load('photo'), // Load lại quan hệ photo để trả về danh sách ảnh cập nhật
        ], 200);
    }

    public function getGalleryDetails($galleries_code)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Tìm gallery theo galleries_code
        $gallery = Gallery::with(['photo.user', 'user'])
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
    public function deletePhotoFromGallery(Request $request, $galleries_code, $photo_id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Find the gallery by code
        $gallery = Gallery::where('galleries_code', $galleries_code)->first();

        if (!$gallery) {
            return response()->json(['message' => 'Gallery not found or access denied'], 404);
        }

        // Check if the user owns the gallery
        if ($gallery->user_id !== $user->id) {
            return response()->json(['message' => 'You do not have permission to modify this gallery'], 403);
        }

        // Check if the photo exists in the gallery
        $photo = $gallery->photo()->where('photo_id', $photo_id)->first();

        if (!$photo) {
            return response()->json(['message' => 'Photo not found in the gallery'], 404);
        }

        // Detach the photo from the gallery
        $gallery->photo()->detach($photo_id);

        return response()->json([
            'message' => 'Photo removed from gallery successfully!',
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
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'name' => 'sometimes|string|max:50|nullable',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'sometimes|string|max:500|nullable',
            'location' => 'sometimes|string|max:255|nullable',
            'profile_picture' => 'sometimes|file|mimes:jpeg,png|max:1024', // JPEG/PNG < 1MB
            'cover_photo' => 'sometimes|file|mimes:jpeg,png|max:2048', // Cover có thể lớn hơn (2MB)
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cập nhật thông tin user nếu có thay đổi
        $updated = false;

        if ($request->has('username') && $request->username !== $user->username) {
            $user->username = $request->username;
            $updated = true;
        }
        if ($request->has('name')) {
            $user->name = $request->name;
            $updated = true;
        }

        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
            $updated = true;
        }

        if ($request->has('bio') && $request->bio !== $user->bio) {
            $user->bio = $request->bio;
            $updated = true;
        }
        if ($request->has('location') && $request->location !== $user->location) {
            $user->location = $request->location;
            $updated = true;
        }

        // Xử lý upload ảnh đại diện (profile_picture)
        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_profile_' . $file->getClientOriginalName();
            $file->move(public_path('images/avatars'), $filename);

            if ($user->profile_picture && file_exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture));
            }

            $user->profile_picture = 'images/avatars/' . $filename;
            $updated = true;
        }

        // Xử lý upload ảnh bìa (cover_photo)
        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = time() . '_cover_' . $file->getClientOriginalName();
            $file->move(public_path('images/covers'), $filename);

            if ($user->cover_photo && file_exists(public_path($user->cover_photo))) {
                unlink(public_path($user->cover_photo));
            }

            $user->cover_photo = 'images/covers/' . $filename;
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
