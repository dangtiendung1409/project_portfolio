<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Block;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    /**
     * Chặn người dùng
     */
    public function blockUser(Request $request)
    {
        $request->validate([
            'blocked_id' => 'required|exists:users,id',
        ]);

        $blocker_id = Auth::id();
        $blocked_id = $request->blocked_id;

        if ($blocker_id == $blocked_id) {
            return response()->json(['message' => 'Bạn không thể tự chặn chính mình.'], 400);
        }

        $existingBlock = Block::where('blocker_id', $blocker_id)
            ->where('blocked_id', $blocked_id)
            ->first();

        if ($existingBlock) {
            return response()->json(['message' => 'Người dùng này đã bị chặn.'], 400);
        }

        // Tạo bản ghi block
        Block::create([
            'blocker_id' => $blocker_id,
            'blocked_id' => $blocked_id,
        ]);

        // Thu hồi follow nếu có: Xóa mối quan hệ follow giữa blocker và blocked (cả 2 chiều)
        Follow::where(function ($query) use ($blocker_id, $blocked_id) {
            $query->where('follower_id', $blocker_id)
                ->where('following_id', $blocked_id);
        })->orWhere(function ($query) use ($blocker_id, $blocked_id) {
            $query->where('follower_id', $blocked_id)
                ->where('following_id', $blocker_id);
        })->delete();

        // Thu hồi các thông báo follow liên quan (giả sử type = 2 là thông báo follow)
        Notification::where(function ($query) use ($blocker_id, $blocked_id) {
            $query->where('user_id', $blocker_id)
                ->where('recipient_id', $blocked_id)
                ->where('type', 2);
        })->orWhere(function ($query) use ($blocker_id, $blocked_id) {
            $query->where('user_id', $blocked_id)
                ->where('recipient_id', $blocker_id)
                ->where('type', 2);
        })->delete();

        return response()->json(['message' => 'Chặn người dùng thành công.'], 200);
    }

    /**
     * Bỏ chặn người dùng
     */
    public function unblockUser(Request $request)
    {
        $request->validate([
            'blocked_id' => 'required|exists:users,id',
        ]);

        $blocker_id = Auth::id();
        $blocked_id = $request->blocked_id;

        // Kiểm tra xem người dùng đã bị chặn chưa
        $block = Block::where('blocker_id', $blocker_id)
            ->where('blocked_id', $blocked_id)
            ->first();

        if (!$block) {
            return response()->json(['message' => 'Người dùng này không bị chặn.'], 400);
        }

        // Xóa bản ghi khỏi bảng blocks
        Block::where('blocker_id', $blocker_id)
            ->where('blocked_id', $blocked_id)
            ->delete();

        return response()->json(['message' => 'Bỏ chặn người dùng thành công.'], 200);
    }

    /**
     * Lấy danh sách những người dùng bị chặn
     */
    public function getBlockedUsers()
    {
        $blockedUsers = Block::where('blocker_id', Auth::id())
            ->with('blocked:id,username,profile_picture')
            ->get()
            ->pluck('blocked');

        return response()->json($blockedUsers, 200);
    }
}
