<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Photo;
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

            // ✅ Lấy Bearer Token từ headers
            $bearerToken = $request->bearerToken();
            Log::info('Bearer Token:', ['token' => $bearerToken]);
            $userId = null;

            if ($bearerToken) {
                try {
                    // ✅ Giải mã JWT của user
                    $decoded = JWT::decode($bearerToken, new Key(env('JWT_SECRET'), 'HS256'));
                    $userId = $decoded->sub ?? null;
                    Log::info('Decoded JWT:', ['decoded' => $decoded]);
                } catch (\Exception $e) {
                    Log::error('Invalid JWT:', ['error' => $e->getMessage()]);
                }
            }

            // ✅ Lưu lượt xem vào Redis
            $ipAddress = $request->ip();
            $cacheKey = "photo_view:{$photo->id}:{$ipAddress}";

            if (!Redis::get($cacheKey)) {
                Redis::setex($cacheKey, 3600, true);
                DB::table('photos')->where('id', $photo->id)->increment('total_views');

                $viewDetails = [
                    'photo_id' => $photo->id,
                    'user_id' => $userId,  // ✅ Lấy user_id từ JWT
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

}
