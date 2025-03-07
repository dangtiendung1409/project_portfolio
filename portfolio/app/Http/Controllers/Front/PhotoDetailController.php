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
    public function getPhotoLikes($token)
    {
        // Tìm photo dựa vào photo_token và eager load mối quan hệ likes.user
        $photo = Photo::where('photo_token', $token)
            ->with(['likes.user'])
            ->first();

        if (!$photo) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found'
            ], 404);
        }

        // Đếm tổng số like của photo đó
        $totalLikes = $photo->likes()->count();

        // Lấy thông tin các user đã like và thêm followers_count cho mỗi user
        $likedUsers = $photo->likes->map(function($like) {
            $user = $like->user;
            // Tính số lượng followers cho user bằng cách đếm số bản ghi trong Follow có following_id = user->id
            $followersCount = Follow::where('following_id', $user->id)->count();
            return array_merge($user->toArray(), ['followers_count' => $followersCount]);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'photo_token'  => $photo->photo_token,
                'total_likes'  => $totalLikes,
                'liked_users'  => $likedUsers
            ]
        ], 200);
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
        // Tìm ảnh chi tiết theo token
        $photo = Photo::where('photo_token', $token)->first();

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        // Lấy danh sách tag của ảnh
        $tagIds = $photo->tags()->pluck('tags.id')->toArray();

        try {
            // Lấy user hiện tại để kiểm tra danh sách người bị chặn
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');

            // Tìm ảnh có tag tương tự và không thuộc về người dùng bị chặn
            $relatedPhotos = Photo::whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
                ->where('id', '!=', $photo->id) // Loại trừ ảnh gốc
                ->whereNotIn('user_id', $blockedUserIds) // Loại trừ ảnh của người dùng bị chặn
                ->with('user') // Lấy toàn bộ thông tin user
                ->limit(10)
                ->get();

            // Nếu không đủ 10 ảnh, lấy thêm từ category
            if ($relatedPhotos->count() < 10) {
                $remaining = 10 - $relatedPhotos->count();
                $categoryPhotos = Photo::where('category_id', $photo->category_id)
                    ->where('id', '!=', $photo->id)
                    ->whereNotIn('user_id', $blockedUserIds) // Loại trừ ảnh của người dùng bị chặn
                    ->with('user') // Lấy toàn bộ thông tin user
                    ->limit($remaining)
                    ->get();

                $relatedPhotos = $relatedPhotos->merge($categoryPhotos);
            }

            return response()->json($relatedPhotos);
        } catch (\Exception $e) {
            // Nếu không có token hoặc xảy ra lỗi, trả về ảnh mà không lọc người dùng bị chặn
            $relatedPhotos = Photo::whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
                ->where('id', '!=', $photo->id)
                ->with('user')
                ->limit(10)
                ->get();

            if ($relatedPhotos->count() < 10) {
                $remaining = 10 - $relatedPhotos->count();
                $categoryPhotos = Photo::where('category_id', $photo->category_id)
                    ->where('id', '!=', $photo->id)
                    ->with('user')
                    ->limit($remaining)
                    ->get();

                $relatedPhotos = $relatedPhotos->merge($categoryPhotos);
            }

            return response()->json($relatedPhotos);
        }
    }
    public function getRelatedGalleries($token)
    {
        // Tìm ảnh chi tiết theo token
        $photo = Photo::where('photo_token', $token)->first();

        if (!$photo) {
            return response()->json(['message' => 'Photo not found'], 404);
        }

        // Lấy danh sách tag của ảnh
        $tagIds = $photo->tags()->pluck('tags.id')->toArray();

        try {
            // Lấy user hiện tại để kiểm tra danh sách người bị chặn
            $currentUser = JWTAuth::parseToken()->authenticate();
            $blockedUserIds = $currentUser->blockedUsers()->pluck('blocked_id');

            // Tìm gallery có ảnh chứa tag tương tự, visibility = 0, và không thuộc về người dùng bị chặn
            $relatedGalleries = Gallery::where('visibility', 0) // Lọc gallery public
            ->whereNotIn('user_id', $blockedUserIds) // Loại trừ gallery của người dùng bị chặn
            ->whereHas('photo.tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            })
                ->with([
                    'photo' => function ($query) use ($blockedUserIds) {
                        $query->select('photos.id', 'photos.image_url')
                            ->whereNotIn('user_id', $blockedUserIds); // Loại trừ ảnh của người dùng bị chặn
                    },
                    'user' => function ($query) {
                        $query->select('id', 'username', 'name', 'profile_picture');
                    }
                ])
                ->get()
                ->filter(function ($gallery) {
                    return $gallery->photo->count() >= 4; // Chỉ lấy gallery có từ 4 ảnh trở lên (sau khi lọc)
                })
                ->take(3) // Giới hạn 3 gallery
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
                                'image_url' => $photo->image_url
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
        } catch (\Exception $e) {
            // Nếu không có token hoặc lỗi, trả về gallery mà không lọc người dùng bị chặn
            $relatedGalleries = Gallery::where('visibility', 0)
                ->whereHas('photo.tags', function ($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                })
                ->with([
                    'photo' => function ($query) {
                        $query->select('photos.id', 'photos.image_url');
                    },
                    'user' => function ($query) {
                        $query->select('id', 'username', 'name', 'profile_picture');
                    }
                ])
                ->get()
                ->filter(function ($gallery) {
                    return $gallery->photo->count() >= 4;
                })
                ->take(3)
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
                                'image_url' => $photo->image_url
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

}
