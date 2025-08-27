@extends('admin.layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-900 py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-lock text-2xl text-white"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Admin Login</h2>
            <p class="text-gray-400">Sign in to access the dashboard</p>
        </div>
        
        <div class="bg-gray-800 rounded-xl p-8 border border-gray-700">
            <form action="{{ url('/admin/login') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address</label>
                        <input id="email" name="email" type="email" required 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
                               placeholder="Enter your email"
                               value="{{ old('email') }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-400 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                        <input id="password" name="password" type="password" required 
                               class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" 
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-400 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox" 
                           class="h-4 w-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500">
                    <label for="remember_me" class="ml-3 text-sm text-gray-300">
                        Remember me for 30 days
                    </label>
                </div>

                <button type="submit" 
                        class="w-full flex items-center justify-center gap-3 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-800 transition-colors">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </button>
            </form>
        </div>
        
        <p class="text-center text-xs text-gray-500">
            © {{ date('Y') }} Admin Panel. All rights reserved.
        </p>
    </div>
</div>
@endsection
