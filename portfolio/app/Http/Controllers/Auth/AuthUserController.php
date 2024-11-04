<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthUserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if ($user->is_active == 0) {
                return response()->json(['login' => 'Your account is locked.'], 403);
            }

            if (Auth::attempt($credentials)) {
                $token = JWTAuth::fromUser($user); // Tạo token JWT
                return response()->json([
                    'message' => 'Login successful',
                    'token' => $token, // Trả token về client
                    'route' => url('/')
                ], 200);
            }
        }

        return response()->json(['login' => 'Wrong account or password.'], 401);
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
        Auth::logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }

}
