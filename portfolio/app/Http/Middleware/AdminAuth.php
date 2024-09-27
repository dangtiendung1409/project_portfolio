<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            // Nếu chưa đăng nhập, chuyển về trang login
            return redirect('login');
        }

        $user = Auth::user();

        // Kiểm tra vai trò người dùng có khớp với yêu cầu không
        if ($user->hasRole($role)) {
            return $next($request);
        }

        // Nếu người dùng không có quyền, chuyển về trang login với thông báo lỗi
        return redirect('login')->withErrors(['You do not have access to this section.']);
    }
}

