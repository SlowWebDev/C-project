<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    /**
     * Handle the login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            SecurityEvent::logEvent(
                'rate_limit_exceeded',
                SecurityEvent::STATUS_BLOCKED,
                null,
                'Too many login attempts from IP: ' . $request->ip()
            );
            
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Log successful login
            SecurityEvent::logEvent(
                'login',
                SecurityEvent::STATUS_SUCCESS,
                auth()->id(),
                'User successfully logged in'
            );
            
            // Clear rate limiter on successful login
            RateLimiter::clear($key);
            
            return redirect()->route('admin.dashboard');
        }

        // Log failed login attempt
        SecurityEvent::logEvent(
            'failed_login',
            SecurityEvent::STATUS_FAILED,
            $user?->id,
            'Failed login attempt for email: ' . $credentials['email']
        );
        
        // Increment rate limiter
        RateLimiter::hit($key, 300); // 5 minutes

        return back()
            ->withErrors([
                'email' => 'Invalid credentials.',
            ])
            ->onlyInput('email');
    }

    /**
     * Handle the logout request
     */
    public function logout(Request $request)
    {
        $userId = auth()->id();
        
        // Log logout event
        SecurityEvent::logEvent(
            'logout',
            SecurityEvent::STATUS_SUCCESS,
            $userId,
            'User logged out'
        );
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
