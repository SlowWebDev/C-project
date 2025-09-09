<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            $user = Auth::user();
            
            // If 2FA is not enabled, redirect to setup
            if (!$user->hasTwoFactorEnabled()) {
                return redirect()->route('admin.2fa.setup')
                    ->with('info', 'Please complete two-factor authentication setup.');
            }
            
            // If 2FA is enabled but not verified in current session
            if (!request()->session()->get('2fa_verified')) {
                return redirect()->route('admin.2fa.show-verify')
                    ->with('info', 'Please verify your two-factor authentication code.');
            }
            
            // All good, redirect to admin dashboard
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
                'Too many login attempts from IP: ' . $request->ip() . ' - User agent: ' . $request->userAgent()
            );
            
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Log successful login
            SecurityEvent::logEvent(
                'login',
                SecurityEvent::STATUS_SUCCESS,
                $user->id,
                'User successfully logged in'
            );
            
            // Clear rate limiter on successful login
            RateLimiter::clear($key);
            
            // Check 2FA status
            if (!$user->hasTwoFactorEnabled()) {
                // Redirect to 2FA setup for new users
                return redirect()->route('admin.2fa.setup')
                    ->with('info', 'Please set up two-factor authentication to secure your account.');
            } else {
                // Redirect to 2FA verification
                return redirect()->route('admin.2fa.show-verify');
            }
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
        
        // Forget specific session keys
        $request->session()->forget([
            '2fa_verified',
            '2fa_pending_action',
            '2fa_device_id', 
            '2fa_device_name',
            '2fa_return_url',
            'login.lockout_time',
            'intended_url'
        ]);
        
        // Invalidate session and regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Create response with redirect
        $response = redirect()->route('admin.login')
            ->with('success', 'You have been logged out successfully.');
        
        // Clear authentication cookies properly
        $cookiesToClear = [
            'laravel_session',
            'remember_admin_token',
            'admin_remember',
            'XSRF-TOKEN'
        ];
        
        foreach ($cookiesToClear as $cookieName) {
            $response = $response->withCookie(
                cookie()->forget($cookieName)
            );
        }
        
        return $response;
    }

    /**
     * Create new admin account (first time only)
     */
    public function register(Request $request)
    {
        // Check if users already exist
        if (User::count() > 0) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Registration is not available.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Log user creation
        SecurityEvent::logEvent(
            'user_created',
            SecurityEvent::STATUS_SUCCESS,
            $user->id,
            'First admin account created'
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.2fa.setup')
            ->with('success', 'Account created successfully! Please set up two-factor authentication.');
    }
}
