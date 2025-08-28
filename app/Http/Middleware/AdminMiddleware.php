<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }
        
        if ($request->ajax()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return redirect()->route('admin.login');
    }
}
