<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.signIn');
    }

    public function login(Request $request)
    {
        // Lấy thông tin đăng nhập từ trường 'email' và 'password'
        $credentials = $request->only('email', 'password');

        // Kiểm tra nếu tài khoản tồn tại nhưng không đúng mật khẩu
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            // Kiểm tra nếu người dùng có quyền là user
            if ($user->hasRole('user')) {
                return back()->withErrors(['login' => 'Wrong account and password.']);
            }

            // Kiểm tra xác thực
            if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('user.dashboard');
            }
        }

        // Nếu không tìm thấy user hoặc sai mật khẩu
        return back()->withErrors(['login' => 'Wrong account and password.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
