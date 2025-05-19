
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Check if user is already logged in
        if (Auth::check()) {
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }
        
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        
        $username = $request->input('username');
        $password = $request->input('password');
        
        // Check if login is with username or email
        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if (Auth::attempt([$field => $username, 'password' => $password], $request->filled('remember'))) {
            // Login successful
            $user = Auth::user();
            
            // Update last login
            $user->last_login = now();
            $user->save();
            
            // Track login activity
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard')->with('success', 'Login successful. Welcome to the admin panel!');
            } else {
                return redirect()->route('user.dashboard')->with('success', 'Login successful!');
            }
        }
        
        // If login fails
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username'));
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
