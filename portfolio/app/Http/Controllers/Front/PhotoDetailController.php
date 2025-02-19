<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhotoDetailController extends Controller
{
    public function getPhotoDetail(Request $request, $token)
    {
        try {
            $photo = Photo::with(['user', 'category'])->where('photo_token', $token)->first();

            if (!$photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }

            // Lấy Bearer Token từ headers
            $bearerToken = $request->bearerToken();
            Log::info('Bearer Token:', ['token' => $bearerToken]);
            $userId = null;

            if ($bearerToken) {
                try {
                    //Giải mã JWT của user
                    $decoded = JWT::decode($bearerToken, new Key(env('JWT_SECRET'), 'HS256'));
                    $userId = $decoded->sub ?? null;
                    Log::info('Decoded JWT:', ['decoded' => $decoded]);
                } catch (\Exception $e) {
                    Log::error('Invalid JWT:', ['error' => $e->getMessage()]);
                }
            }

            // Lưu lượt xem vào Redis
            $ipAddress = $request->ip();
            $cacheKey = "photo_view:{$photo->id}:{$ipAddress}";

            if (!Redis::get($cacheKey)) {
                Redis::setex($cacheKey, 3600, true);
                DB::table('photos')->where('id', $photo->id)->increment('total_views');

                $viewDetails = [
                    'photo_id' => $photo->id,
                    'user_id' => $userId,
                    'viewed_at' => now()->toDateTimeString(),
                ];
                Redis::lpush("photo_view_details:{$photo->id}", json_encode($viewDetails));
            }

            return response()->json([
                'data' => $photo,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching photo details: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function getCommentsByPhotoToken($token)
    {
        try {
            $photo = Photo::where('photo_token', $token)->first();

            if (!$photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }

            // Thêm phương thức orderBy để sắp xếp các bình luận theo created_at (mới nhất trước)
            $comments = $photo->comments()->with('user')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'data' => $comments,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching comments: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function postComment(Request $request)
    {
        $this->validate($request, [
            'photo_token' => 'required|string|exists:photos,photo_token',
            'comment_text' => 'required|string',
        ]);

        try {
            $photo = Photo::where('photo_token', $request->photo_token)->first();

            if (!$photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }

            // Lấy Bearer Token từ headers
            $bearerToken = $request->bearerToken();
            Log::info('Bearer Token:', ['token' => $bearerToken]);
            $userId = null;

            if ($bearerToken) {
                try {
                    $decoded = JWT::decode($bearerToken, new Key(env('JWT_SECRET'), 'HS256'));
                    $userId = $decoded->sub ?? null;
                    Log::info('Decoded JWT:', ['decoded' => $decoded]);
                } catch (\Exception $e) {
                    Log::error('Invalid JWT:', ['error' => $e->getMessage()]);
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
            }

            if (!$userId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Tạo comment
            $comment = new Comment();
            $comment->photo_id = $photo->id;
            $comment->user_id = $userId;
            $comment->comment_text = $request->comment_text;
            $comment->save();

            // Gửi thông báo cho chủ sở hữu ảnh
            if ($photo->user_id != $userId) { // Không gửi nếu người bình luận là chủ ảnh
                Notification::create([
                    'user_id' => $userId, // Người gửi (người bình luận)
                    'recipient_id' => $photo->user_id, // Chủ ảnh (người nhận)
                    'comment_id' => $comment->id,
                    'photo_id' => $photo->id,
                    'like_id' => null, // Không liên quan đến like
                    'type' => '1',
                    'content' => User::find($userId)->username . ' has commented on your photo.',
                    'is_read' => 0,
                    'notification_date' => now(),
                ]);
            }

            // Load relationship với user và trả về comment với thông tin user
            $comment = Comment::with('user')->find($comment->id);

            return response()->json([
                'message' => 'Comment posted successfully',
                'comment' => $comment
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error posting comment: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

}
