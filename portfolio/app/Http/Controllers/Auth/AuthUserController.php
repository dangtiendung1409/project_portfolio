<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
class AuthUserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Wrong account or password.'], 401);
        }

        $user = JWTAuth::user();

        if ($user->is_active == 0) {
            return response()->json(['error' => 'Your account is locked.'], 403);
        }

        // Tạo refresh token với thời hạn khác
        $refreshToken = JWTAuth::customClaims(['exp' => now()->addMinutes(env('JWT_REFRESH_TTL', 20160))->timestamp])->fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'refresh_token' => $refreshToken,
            'route' => url('/')
        ], 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
        ]);

        $user = User::create([
            'username' => $request->input('fullName'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role_id' => 2,
            'is_active' => 1,
            'violation_count' => 0,
            'join_date' => now(),
            'user_token' => Str::uuid()->toString(),
        ]);

        return response()->json(['message' => 'Registration successful'], 201);
    }

    public function logout(Request $request)
    {
        try {
            // Lấy access token từ request
            $token = JWTAuth::getToken();
            if ($token) {
                try {
                    // Invalidate token (kể cả nếu hết hạn)
                    JWTAuth::invalidate($token);
                } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    // Nếu token đã hết hạn, vẫn cho phép tiếp tục
                }
            }

            return response()->json(['message' => 'Logout successful'], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again.'], 500);
        }
    }


    public function refreshToken(Request $request)
    {
        try {
            // Lấy refresh token từ request (hoặc access token nếu cần)
            $refreshToken = JWTAuth::getToken();
            if (!$refreshToken) {
                return response()->json([
                    'error' => 'Token is missing. Please log in again.',
                ], 401);
            }

            // Xác thực người dùng dựa trên token hiện tại
            $user = JWTAuth::authenticate($refreshToken);
            if (!$user) {
                return response()->json([
                    'error' => 'User not authenticated.',
                ], 401);
            }

            // Tạo token mới trực tiếp từ user
            $newToken = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $newToken,
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Failed to refresh token.',
                'message' => $e->getMessage(),
            ], 401);
        }
    }


    public function getUser(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }
            return response()->json([
                'user' => $user,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Invalid token.'], 401);
        }
    }
}
