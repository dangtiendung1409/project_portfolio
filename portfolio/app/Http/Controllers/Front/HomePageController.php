<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class HomePageController extends Controller
{
    public function addPhotos(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'photos' => 'required|array|max:10',
            'photos.*.title' => 'nullable|string|max:255',
            'photos.*.description' => 'nullable|string',
            'photos.*.location' => 'nullable|string|max:255',
            'photos.*.image' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120', // Validate file ảnh
            'photos.*.category_id' => 'required|exists:categories,id',
            'photos.*.privacy_status' => 'required|string',
            'photos.*.tags' => 'nullable|string', // Thêm tags vào validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $photosData = $request->input('photos');
        $currentDate = Carbon::now(); // Lấy ngày hiện tại

        foreach ($photosData as $index => $photoData) {
            $photoData['user_id'] = $user->id;
            $photoData['upload_date'] = $currentDate; // Đặt ngày hiện tại cho upload_date
            $photoData['photo_status'] = 'pending'; // Đặt giá trị mặc định là pending
            $photoData['photo_token'] = Str::uuid(); // Tạo photo_token tự động

            // Xử lý ảnh
            if ($request->hasFile("photos.$index.image")) {
                $image = $request->file("photos.$index.image");
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('images/photos'), $imageName);
                $photoData['image_url'] = 'images/photos/' . $imageName; // Lưu đường dẫn ảnh
            }

            // Tạo ảnh trong database
            $photo = Photo::create($photoData);

            // **Xử lý tags**
            if (!empty($photoData['tags'])) {
                $tags = explode(',', $photoData['tags']); // Lấy danh sách tag
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName); // Loại bỏ khoảng trắng

                    // Kiểm tra tag đã tồn tại chưa
                    $tag = Tag::firstOrCreate(['tag_name' => $tagName]);

                    // Attach tag vào bảng trung gian
                    $photo->tags()->attach($tag->id);
                }
            }
        }

        return response()->json(['message' => 'Photos added successfully'], 201);
    }
    public function getAllCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    public function getAllTags()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }
    public function getImages(Request $request)
    {
        try {
            // Lấy user hiện tại từ token
            $currentUser = JWTAuth::parseToken()->authenticate();

            // Lấy danh sách user bị chặn
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');

            // Lọc ảnh: loại bỏ ảnh của user bị chặn
            $images = Photo::with(['category', 'user'])
                ->whereNotIn('user_id', $blockedUserIds)
                ->get();
        } catch (\Exception $e) {
            // Nếu không có token, lấy tất cả ảnh
            $images = Photo::with(['category', 'user'])->get();
        }

        return response()->json($images);
    }

    public function getFollows(Request $request)
    {
        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $followedUserIds = $currentUser->followings()->pluck('users.id');
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id')
                ->merge($currentUser->blockedBy()->pluck('blocker_id'));

            // Lấy danh sách user chưa follow và chưa bị block
            $users = User::whereNotIn('id', $followedUserIds)
                ->whereNotIn('id', $blockedUserIds)
                ->where('id', '!=', $currentUser->id)
                ->with(['photos' => function ($query) {
                    $query->where('privacy_status', 0)
                        ->where('photo_status', 'approved');
                }])
                ->get();
        } catch (\Exception $e) {
            // Nếu không có token, lấy toàn bộ danh sách user cùng với ảnh được hiển thị
            $users = User::with(['photos' => function ($query) {
                $query->where('privacy_status', 0)
                    ->where('photo_status', 'approved');
            }])
                ->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }

    public function likePhoto(Request $request)
    {
        $user = Auth::user(); // Người đang đăng nhập
        $photoId = $request->input('photo_id');
        $photoUserId = $request->input('photo_user_id'); // Nhận photo_user_id từ API

        // Nếu không có photo_user_id, tìm user_id từ bảng photos
        if (!$photoUserId) {
            $photo = Photo::find($photoId);
            if (!$photo || !$photo->photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }
            $photoUserId = $photo->photo->user_id;
        }

        // Kiểm tra xem like đã tồn tại chưa
        $like = Like::where('user_id', $user->id)->where('photo_id', $photoId)->first();

        if (!$like) {
            // Tạo like mới
            $like = Like::create([
                'user_id' => $user->id,
                'photo_id' => $photoId,
                'like_date' => now(),
            ]);

            // Chỉ tạo thông báo nếu photoUserId khác với user hiện tại
            if ($photoUserId !== $user->id) {
                Notification::create([
                    'user_id' => $user->id,
                    'recipient_id' => $photoUserId, // Người nhận thông báo (chủ sở hữu bức ảnh)
                    'like_id' => $like->id,
                    'comment_id' => null,
                    'photo_id' => $photoId,
                    'type' => 0,
                    'content' => "{$user->username} liked your photo.",
                    'is_read' => false,
                    'notification_date' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Photo liked successfully'], 200);
    }

    public function unlikePhoto(Request $request)
    {
        $user = Auth::user();
        $photoId = $request->input('photo_id');

        // Lấy bản ghi like
        $like = Like::where('user_id', $user->id)->where('photo_id', $photoId)->first();

        if ($like) {
            // Thu hồi thông báo liên quan trước
            Notification::where('like_id', $like->id)->delete();

            // Xóa like
            $like->delete();
        }

        return response()->json(['message' => 'Photo unliked successfully'], 200);
    }
    public function likeGallery(Request $request)
    {
        $user = Auth::user(); // Người đang đăng nhập
        $galleryId = $request->input('gallery_id');
        $galleryUserId = $request->input('gallery_user_id'); // Nhận gallery_user_id từ API nếu có

        // Nếu không có gallery_user_id, lấy thông tin gallery để xác định chủ sở hữu
        if (!$galleryUserId) {
            $gallery = Gallery::find($galleryId);
            if (!$gallery) {
                return response()->json(['message' => 'Gallery not found'], 404);
            }
            $galleryUserId = $gallery->user_id;
        }

        // Kiểm tra xem like cho gallery này đã tồn tại chưa
        $like = Like::where('user_id', $user->id)
            ->where('gallery_id', $galleryId)
            ->first();

        if (!$like) {
            // Tạo like mới cho gallery
            $like = Like::create([
                'user_id'    => $user->id,
                'gallery_id' => $galleryId,
                'like_date'  => now(),
            ]);

            // Nếu gallery không thuộc về người dùng hiện tại, tạo thông báo
            if ($galleryUserId !== $user->id) {
                Notification::create([
                    'user_id'           => $user->id,
                    'recipient_id'      => $galleryUserId, // Chủ sở hữu gallery
                    'like_id'           => $like->id,
                    'comment_id'        => null,
                    'gallery_id'        => $galleryId,     // Gắn gallery_id cho thông báo
                    'type'              => 3,              // Ví dụ: 1 đại diện cho like gallery (0 là like photo)
                    'content'           => "{$user->username} liked your gallery.",
                    'is_read'           => false,
                    'notification_date' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Gallery liked successfully'], 200);
    }

    public function unlikeGallery(Request $request)
    {
        $user = Auth::user();
        $galleryId = $request->input('gallery_id');

        // Lấy bản ghi like cho gallery
        $like = Like::where('user_id', $user->id)
            ->where('gallery_id', $galleryId)
            ->first();

        if ($like) {
            // Xóa thông báo liên quan
            Notification::where('like_id', $like->id)->delete();

            // Xóa like
            $like->delete();
        }

        return response()->json(['message' => 'Gallery unliked successfully'], 200);
    }

    public function getUserNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('recipient_id', $user->id)
        ->with(['user', 'like', 'comment', 'photo'])
            ->orderBy('notification_date', 'desc')
            ->get();

        return response()->json($notifications);
    }
    public function markNotificationAsRead(Request $request)
    {
        $notificationId = $request->input('notification_id');

        // Tìm thông báo theo ID
        $notification = Notification::find($notificationId);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        // Cập nhật trạng thái is_read
        $notification->is_read = 1;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }


}
