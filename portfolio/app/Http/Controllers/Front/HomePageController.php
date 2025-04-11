<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Follow;
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
use Illuminate\Support\Facades\Mail;
use App\Models\Contact;
use App\Mail\ContactMail;
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
            'photos.*.tags' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $photosData = $request->input('photos');
        $currentDate = Carbon::now();

        foreach ($photosData as $index => $photoData) {
            $photoData['user_id'] = $user->id;
            $photoData['upload_date'] = $currentDate;
            $photoData['photo_status'] = 'pending';
            $photoData['photo_token'] = Str::uuid();

            // Xử lý ảnh
            if ($request->hasFile("photos.$index.image")) {
                $image = $request->file("photos.$index.image");

                // **Lấy tên file gốc**
                $imageName = $image->getClientOriginalName();
                $imagePath = '/images/photos/' . $imageName;

                // **Lưu ảnh**
                $image->move(public_path('/images/photos'), $imageName);

                // **Lưu đường dẫn vào database**
                $photoData['image_url'] = $imagePath;
            }

            // **Tạo bản ghi ảnh trong database**
            $photo = Photo::create($photoData);

            // **Xử lý tags**
            if (!empty($photoData['tags'])) {
                $tags = explode(',', $photoData['tags']);
                foreach ($tags as $tagName) {
                    $tagName = trim($tagName);
                    $tag = Tag::firstOrCreate(['tag_name' => $tagName]);
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
        // Số lượng ảnh mỗi trang, mặc định là 20
        $perPage = $request->input('per_page', 20);
        // Trang hiện tại, mặc định là 1
        $page = $request->input('page', 1);

        try {
            // Lấy user hiện tại từ token
            $currentUser = JWTAuth::parseToken()->authenticate();

            // Lấy danh sách user bị chặn
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');

            // Lọc ảnh: loại bỏ ảnh của user bị chặn, chỉ lấy ảnh approved và public, sắp xếp theo total_views giảm dần
            $images = Photo::with(['user:id,username,name,profile_picture'])
                ->whereNotIn('user_id', $blockedUserIds)
                ->where('photo_status', 'approved')
                ->where('privacy_status', 0)
                ->select('id', 'upload_date', 'image_url', 'photo_token', 'user_id', 'total_views')
                ->orderByDesc('total_views')
                ->paginate($perPage, ['*'], 'page', $page);
        } catch (\Exception $e) {
            // Nếu không có token, lấy tất cả ảnh approved và public
            $images = Photo::with(['user:id,username,name,profile_picture'])
                ->where('photo_status', 'approved')
                ->where('privacy_status', 0)
                ->select('id', 'upload_date', 'image_url', 'photo_token', 'user_id', 'total_views')
                ->orderByDesc('total_views')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        // Định dạng lại dữ liệu trước khi trả về
        $filteredImages = $images->map(function ($image) {
            return [
                'id' => $image->id,
                'upload_date' => $image->upload_date,
                'image_url' => $image->image_url,
                'photo_token' => $image->photo_token,
                'user_id' => $image->user_id,
                'total_views' => $image->total_views,
                'user' => [
                    'id' => $image->user->id,
                    'username' => $image->user->username,
                    'name' => $image->user->name,
                    'profile_picture' => $image->user->profile_picture,
                ],
            ];
        });

        // Trả về dữ liệu phân trang
        return response()->json([
            'data' => $filteredImages,
            'current_page' => $images->currentPage(),
            'last_page' => $images->lastPage(),
            'total' => $images->total(),
            'per_page' => $images->perPage(),
        ]);
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

    public function topLikedPhotos()
    {
        try {
            // Lấy user hiện tại để kiểm tra danh sách người bị chặn
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');
        } catch (\Exception $e) {
            // Nếu không có token, không lọc người bị chặn
            $blockedUserIds = [];
        }

        // Lấy danh sách ảnh nhiều like nhất kèm theo toàn bộ thông tin user
        $photos = Photo::with(['user']) // Lấy toàn bộ thông tin user của ảnh
        ->withCount('likes') // Đếm số lượt like
        ->whereNotIn('user_id', $blockedUserIds) // Bỏ qua ảnh của người bị chặn
        ->where('photo_status', 'approved') // Chỉ lấy ảnh đã được duyệt
        ->where('privacy_status', 0) // Chỉ lấy ảnh công khai
        ->orderBy('likes_count', 'desc')
            ->take(12)
            ->get();

        // Nếu chưa đủ 10 ảnh, lấy thêm ảnh hợp lệ khác để bù
        if ($photos->count() < 12) {
            $remaining = 12 - $photos->count();
            $extraPhotos = Photo::with(['user']) // Lấy toàn bộ thông tin user của ảnh
            ->withCount('likes')
                ->whereNotIn('id', $photos->pluck('id')) // Không lấy ảnh đã có
                ->whereNotIn('user_id', $blockedUserIds) // Bỏ qua ảnh của người bị chặn
                ->where('photo_status', 'approved') // Chỉ lấy ảnh đã được duyệt
                ->where('privacy_status', 0) // Chỉ lấy ảnh công khai
                ->orderBy('likes_count', 'desc')
                ->take($remaining)
                ->get();

            $photos = $photos->merge($extraPhotos);
        }

        return response()->json([
            'status' => 'success',
            'data' => $photos
        ]);
    }
    public function getTopUsersWithPhotos()
    {
        try {
            // Lấy user hiện tại để kiểm tra danh sách người bị chặn
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');
        } catch (\Exception $e) {
            // Nếu không có token, không lọc người bị chặn
            $blockedUserIds = [];
        }

        // Lấy danh sách user có tổng số like ảnh cao nhất (bỏ qua người bị block)
        $topUsers = User::select('users.id', 'users.username', 'users.name', 'users.profile_picture')
            ->withCount([
                'photos as total_likes' => function ($query) {
                    $query->where('photo_status', 'approved')
                        ->where('privacy_status', 0)
                        ->join('likes', 'photos.id', '=', 'likes.photo_id');
                }
            ])
            ->whereNotIn('id', $blockedUserIds) // Bỏ qua user bị block
            ->orderByDesc('total_likes')
            ->limit(12)
            ->get();

        // Nếu chưa đủ 10 user, lấy thêm user hợp lệ khác để bù
        if ($topUsers->count() < 12) {
            $remaining = 12 - $topUsers->count();
            $extraUsers = User::select('users.id', 'users.username', 'users.name', 'users.profile_picture')
                ->withCount([
                    'photos as total_likes' => function ($query) {
                        $query->where('photo_status', 'approved')
                            ->where('privacy_status', 0)
                            ->join('likes', 'photos.id', '=', 'likes.photo_id');
                    }
                ])
                ->whereNotIn('id', $blockedUserIds) // Bỏ qua user bị block
                ->whereNotIn('id', $topUsers->pluck('id')) // Không lấy user đã có
                ->orderByDesc('total_likes')
                ->limit($remaining)
                ->get();

            $topUsers = $topUsers->merge($extraUsers);
        }

        // Lấy danh sách ảnh của từng user trong danh sách top
        $topUsers->each(function ($user) {
            $user->photos = Photo::where('user_id', $user->id)
                ->where('photo_status', 'approved')
                ->where('privacy_status', 0)
                ->withCount('likes')
                ->get();
        });

        return response()->json([
            'status' => 'success',
            'top_users' => $topUsers
        ]);
    }
    public function getTopCategories()
    {
        // Lấy 8 category có nhiều ảnh nhất
        $categories = Category::withCount('photos')
            ->orderByDesc('photos_count')
            ->limit(8)
            ->get();

        return response()->json($categories);
    }
    public function getTopLikedGalleries()
    {
        try {
            // Lấy user hiện tại để kiểm tra danh sách người bị chặn
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');
        } catch (\Exception $e) {
            $blockedUserIds = [];
        }

        // Lấy danh sách gallery có nhiều like nhất (bỏ qua gallery của user bị block)
        $galleries = Gallery::where('visibility', 0)
            ->whereNotIn('user_id', $blockedUserIds)
            ->withCount('likes')
            ->with([
                'photo' => function ($query) {
                    $query->select('photos.id', 'photos.image_url');
                },
                'user' => function ($query) {
                    $query->select('id', 'username', 'name', 'profile_picture');
                }
            ])
            ->orderByDesc('likes_count')
            ->limit(9)
            ->get()
            ->each(function ($gallery) {
                $gallery->photo->makeHidden(['pivot']);
            });

        return response()->json([
            'status' => 'success',
            'data' => $galleries
        ]);
    }

    public function getRecentFollowedPhotos()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lấy danh sách user mà người dùng đang follow
        $followingUsers = Follow::where('follower_id', $user->id)->pluck('following_id');

        if ($followingUsers->isEmpty()) {
            return response()->json(['message' => 'No followed users found'], 200);
        }

        // Lấy tối đa 3 ảnh gần đây nhất của mỗi user đang follow, kèm thông tin user
        $photos = Photo::whereIn('user_id', $followingUsers)
            ->with('user:id,username,name,profile_picture') // Lấy thông tin user cần thiết
            ->orderBy('upload_date', 'desc')
            ->get()
            ->groupBy('user_id') // Nhóm theo user_id
            ->map(function ($photos) {
                return $photos->take(2); // Mỗi user lấy tối đa 3 ảnh
            })
            ->flatten(); // Chuyển về một danh sách ảnh duy nhất

        return response()->json($photos);
    }
    public function getRecentFollowedGalleries()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lấy danh sách user mà người dùng đang follow
        $followingUsers = Follow::where('follower_id', $user->id)->pluck('following_id');

        if ($followingUsers->isEmpty()) {
            return response()->json(['message' => 'No followed users found'], 200);
        }

        // Lấy tối đa 3 gallery gần đây nhất của mỗi user đang follow, kèm thông tin user và ảnh
        $galleries = Gallery::whereIn('user_id', $followingUsers)
            ->with([
                'user:id,username,name,profile_picture',
                'photo:image_url'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('user_id') // Nhóm theo user_id
            ->map(function ($galleries) {
                return $galleries->take(1); // Mỗi user lấy tối đa 3 gallery
            })
            ->flatten(); // Chuyển về một danh sách gallery duy nhất

        return response()->json($galleries);
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
            ->with(['user', 'like', 'comment', 'photo', 'gallery'])
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
    public function sendContact(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        // Lưu vào database
        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'contact_date' => now(),
            'status' => 'pending',
        ]);

        // Gửi email đến admin
        Mail::to('dungprohn1409@gmail.com')->send(new ContactMail($request->all()));

        return response()->json([
            'message' => 'Contact form submitted successfully!',
            'data' => $contact
        ], 201);
    }

}
