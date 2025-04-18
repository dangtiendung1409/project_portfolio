<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Gallery;
use App\Models\Notification;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
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
            $photo = Photo::with([
                'user:id,username,profile_picture,bio',
                'category:id,category_name'
            ])
                ->select('id', 'photo_token', 'title', 'description', 'location', 'upload_date', 'total_views', 'image_url', 'user_id', 'category_id')
                ->where('photo_token', $token)
                ->first();

            if (!$photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }

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
                }
            }

            // Ghi nhận lượt xem bằng Redis
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
                'data' => [
                    'id' => $photo->id,
                    'title' => $photo->title,
                    'description' => $photo->description,
                    'location' => $photo->location,
                    'upload_date' => $photo->upload_date,
                    'total_views' => $photo->total_views,
                    'image_url' => $photo->image_url,
                    'liked' => false, // frontend sẽ cập nhật bằng store
                    'user' => $photo->user,
                    'category' => $photo->category,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching photo details: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function getPhotoLikes(Request $request, $token)
    {
        try {
            // Validate input
            $perPage = (int) $request->input('per_page', 8);
            $page = (int) $request->input('page', 1);
            if ($perPage < 1 || $page < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid pagination parameters'
                ], 400);
            }

            // Tìm photo
            $photo = Photo::where('photo_token', $token)
                ->with(['likes.user'])
                ->first();

            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Photo not found'
                ], 404);
            }

            // Đếm tổng số like
            $totalLikes = $photo->likes()->count();

            // Lấy danh sách người dùng đã like với phân trang
            $likes = $photo->likes()
                ->with(['user:id,username,profile_picture'])
                ->select('id', 'user_id', 'photo_id')
                ->paginate($perPage, ['*'], 'page', $page);

            // Định dạng dữ liệu người dùng
            $likedUsers = $likes->filter(function ($like) {
                return !is_null($like->user); // Loại bỏ like không có user
            })->map(function ($like) {
                $user = $like->user;
                $followersCount = $user->followers()->count();

                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'profile_picture' => $user->profile_picture,
                    'followers_count' => $followersCount,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'photo_token' => $photo->photo_token,
                    'total_likes' => $totalLikes,
                    'liked_users' => $likedUsers->values()->all(),
                    'current_page' => $likes->currentPage(),
                    'last_page' => $likes->lastPage(),
                    'total' => $likes->total(),
                    'per_page' => $likes->perPage(),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching photo likes: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error'
            ], 500);
        }
    }

    public function getCommentsByPhotoToken(Request $request, $token)
    {
        try {
            $photo = Photo::where('photo_token', $token)->first();

            if (!$photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }

            // Số lượng bình luận mỗi trang, mặc định là 3
            $perPage = $request->input('per_page', 3);
            // Trang hiện tại, mặc định là 1
            $page = $request->input('page', 1);

            // Lấy comments với user (giới hạn field)
            $comments = $photo->comments()
                ->with(['user:id,name,profile_picture'])
                ->select('id', 'comment_text', 'created_at', 'user_id', 'photo_id')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $comments->items(),
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'total' => $comments->total(),
                'per_page' => $comments->perPage(),
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
            $user = Auth::user(); // Lấy user từ auth:api
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $photo = Photo::where('photo_token', $request->photo_token)->first();
            if (!$photo) {
                return response()->json(['message' => 'Photo not found'], 404);
            }

            // Tạo comment
            $comment = new Comment();
            $comment->photo_id = $photo->id;
            $comment->user_id = $user->id;
            $comment->comment_text = $request->comment_text;
            $comment->save();

            // Gửi thông báo cho chủ sở hữu ảnh nếu người bình luận không phải chủ ảnh
            if ($photo->user_id !== $user->id) {
                Notification::create([
                    'user_id' => $user->id, // Người gửi (người bình luận)
                    'recipient_id' => $photo->user_id, // Chủ ảnh (người nhận)
                    'comment_id' => $comment->id,
                    'photo_id' => $photo->id,
                    'like_id' => null, // Không liên quan đến like
                    'type' => '1',
                    'content' => "{$user->username} has commented on your photo.",
                    'is_read' => 0,
                    'notification_date' => now(),
                ]);
            }

            // Load relationship với user và trả về comment với thông tin user
            $comment->load('user');

            return response()->json([
                'message' => 'Comment posted successfully',
                'comment' => $comment
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error posting comment: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function deleteComment($id)
    {
        try {
            $user = Auth::user(); // Lấy thông tin user từ auth:api

            $comment = Comment::find($id);

            if (!$comment) {
                return response()->json(['message' => 'Comment not found'], 404);
            }

            // Kiểm tra nếu user là chủ sở hữu của comment hoặc chủ của ảnh
            if ($comment->user_id !== $user->id && $comment->photo->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            // Xóa tất cả thông báo liên quan đến comment này
            Notification::where('comment_id', $comment->id)->delete();

            // Xóa comment
            $comment->delete();

            return response()->json(['message' => 'Comment deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
    public function getRelatedPhotos($token)
    {
        // Tìm ảnh chi tiết
        $photo = Photo::where('photo_token', $token)->first();
        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $blockedUserIds = [];

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id')->toArray();
        } catch (\Exception $e) {
            // Không có user => không lọc blocked
        }

        // Lấy ảnh trong cùng category, loại trừ ảnh gốc, user bị chặn, ưu tiên nhiều like nhất
        $relatedPhotos = Photo::withCount('likes')
            ->where('category_id', $photo->category_id)
            ->where('id', '!=', $photo->id)
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                return $query->whereNotIn('user_id', $blockedUserIds);
            })
            ->with('user')
            ->orderByDesc('likes_count')
            ->limit(10)
            ->get();

        return response()->json($relatedPhotos);
    }
    public function getRelatedGalleries($token)
    {
        $photo = Photo::where('photo_token', $token)->first();

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        $categoryId = $photo->category_id;

        try {
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');
        } catch (\Exception $e) {
            $blockedUserIds = [];
        }

        $relatedGalleries = Gallery::where('visibility', 0)
            ->whereHas('photo', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                $query->whereNotIn('user_id', $blockedUserIds);
            })
            ->withCount('likes')
            ->with([
                'photo' => function ($q) use ($blockedUserIds) {
                    $q->select('id', 'image_url', 'user_id')
                        ->when(!empty($blockedUserIds), function ($query) use ($blockedUserIds) {
                            $query->whereNotIn('user_id', $blockedUserIds);
                        });
                },
                'user:id,username,name,profile_picture'
            ])
            ->get()
            ->filter(function ($gallery) {
                return $gallery->photo->count() >= 4;
            })
            ->sortByDesc('likes_count')
            ->take(3)
            ->values()
            ->map(function ($gallery) {
                return [
                    'id' => $gallery->id,
                    'galleries_name' => $gallery->galleries_name,
                    'galleries_description' => $gallery->galleries_description,
                    'user_id' => $gallery->user_id,
                    'visibility' => $gallery->visibility,
                    'galleries_code' => $gallery->galleries_code,
                    'created_at' => $gallery->created_at,
                    'updated_at' => $gallery->updated_at,
                    'photos' => $gallery->photo->map(function ($photo) {
                        return [
                            'id' => $photo->id,
                            'image_url' => $photo->image_url,
                        ];
                    }),
                    'user' => [
                        'id' => $gallery->user->id,
                        'username' => $gallery->user->username,
                        'name' => $gallery->user->name,
                        'profile_picture' => $gallery->user->profile_picture
                    ]
                ];
            });

        if ($relatedGalleries->isEmpty()) {
            return response()->json(['message' => 'No related galleries found'], 404);
        }

        return response()->json($relatedGalleries);
    }


}
