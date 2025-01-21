<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class HomePageController extends Controller
{
    public function getImages()
    {
        $images = Photo::with(['category', 'user'])->get();
        return response()->json($images);
    }
    public function getFollows() {
        // Lấy tất cả users cùng với 4 ảnh đầu tiên từ tất cả các photo của họ
        $follows = User::with(['photos.images' => function ($query) {
            $query->limit(4); // Giới hạn chỉ lấy 4 ảnh đầu tiên từ tất cả các photo
        }])->get();

        // Khai báo biến chứa kết quả cuối cùng
        $result = $follows->map(function ($user) {
            // Lấy ra tất cả ảnh từ các photo của user
            $allImages = $user->photos->flatMap(function ($photo) {
                return $photo->images; // Lấy tất cả ảnh từ từng photo
            });

            // Lấy ra 4 ảnh đầu tiên từ tất cả ảnh của user
            $user->images = $allImages->take(4); // Lấy 4 ảnh đầu tiên
            return $user; // Trả về user với 4 ảnh
        });

        return response()->json($result);
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
