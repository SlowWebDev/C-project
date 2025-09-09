<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Authentication Middleware
 * 
 * Ensures only authenticated admin users can access admin routes
 * 
 * @author SlowWebDev
 */
class AdminMiddleware
{
    /**
     * Handle incoming admin requests
     * Check if user is authenticated, redirect to login if not
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (Auth::check()) {
            return $next($request);
        }
        
        // Handle AJAX requests with JSON response
        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Redirect to admin login page
        return redirect()->route('admin.login');
    }
}
