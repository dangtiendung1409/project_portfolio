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
        // Get login credentials from 'email' and 'password' fields
        $credentials = $request->only('email', 'password');

        // Check if the user exists
        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            // Check if the user account is active
            if ($user->is_active == 0) {
                return back()->withErrors(['login' => 'Your account is locked.']);
            }

            // Check if the user has the role 'user'
            if ($user->hasRole('user')) {
                return back()->withErrors(['login' => 'Wrong account and password.']);
            }

            // Authenticate the user
            if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('user.dashboard');
            }
        }

        // If the user is not found or the password is incorrect
        return back()->withErrors(['login' => 'Wrong account and password.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
