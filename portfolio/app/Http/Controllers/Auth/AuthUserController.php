<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
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
        ]);

        return response()->json(['message' => 'Registration successful'], 201);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken()); // Thu hồi token hiện tại
            return response()->json(['message' => 'Logout successful'], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            // Kiểm tra và làm mới token
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $newToken
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to refresh token.'], 500);
        }
    }
    public function getUser(Request $request)
    {
        try {
            // Lấy thông tin người dùng từ token JWT
            $user = JWTAuth::parseToken()->authenticate(); // Giải mã token và lấy người dùng

            // Kiểm tra nếu người dùng không hợp lệ
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }

            // Trả về thông tin người dùng
            return response()->json([
                'user' => $user,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Invalid token.'], 401);
        }
    }
}
