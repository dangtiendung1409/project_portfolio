<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Theo dõi một người dùng
     */
    public function follow(Request $request)
    {
        $user = Auth::user();
        $followingId = $request->input('following_id');

        if ($user->id == $followingId) {
            return response()->json(['message' => 'Bạn không thể theo dõi chính mình!'], 400);
        }

        $alreadyFollowing = Follow::where('follower_id', $user->id)
            ->where('following_id', $followingId)
            ->exists();

        if ($alreadyFollowing) {
            return response()->json(['message' => 'Bạn đã theo dõi người dùng này!'], 400);
        }

        // Tạo bản ghi Follow
        Follow::create([
            'follower_id' => $user->id,
            'following_id' => $followingId,
            'follow_date' => now(),
        ]);

        // Gửi thông báo follow
        Notification::create([
            'user_id' => $user->id, // Người follow
            'recipient_id' => $followingId, // Người nhận thông báo (bị follow)
            'type' => 2, // Giả sử type = 2 là thông báo follow
            'content' => "{$user->name} followed you.",
            'is_read' => false,
            'notification_date' => now(),
        ]);

        return response()->json(['message' => 'Đã theo dõi thành công!']);
    }
    /**
     * Hủy theo dõi một người dùng
     */
    public function unfollow($followingId)
    {
        $user = Auth::user();

        // Tìm và xóa bản ghi follow
        $deleted = Follow::where('follower_id', $user->id)
            ->where('following_id', $followingId)
            ->delete();

        if ($deleted) {
            // Xóa thông báo follow liên quan
            Notification::where('user_id', $user->id)
                ->where('recipient_id', $followingId)
                ->where('type', 2) // Giả sử type = 2 là follow
                ->delete();

            return response()->json(['message' => 'Đã hủy theo dõi!'], 200);
        } else {
            return response()->json(['message' => 'Bạn chưa theo dõi người dùng này!'], 400);
        }
    }


    /**
     * Danh sách người dùng mà user hiện tại đang theo dõi
     */
    public function followingList()
    {
        $user = Auth::user();
        $following = $user->followings()->get();

        return response()->json($following);
    }

    /**
     * Danh sách những người theo dõi user hiện tại
     */
    public function followersList()
    {
        $user = Auth::user();
        $followers = $user->followers()->get();

        return response()->json($followers);
    }
}
