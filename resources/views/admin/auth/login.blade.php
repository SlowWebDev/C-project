{{--
    Admin Login & Registration Page
    
    Handles both first-time admin registration and subsequent logins.
    Features split-screen design with secure form validation.
    
    Author: SlowWebDev
--}}

@extends('admin.layouts.auth')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    @php
        $noUsers = \App\Models\User::count() === 0;
    @endphp
    
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <div class="w-full max-w-md space-y-6 lg:space-y-8">
            <div class="text-center">
                <h1 class="text-white font-extralight text-2xl sm:text-3xl lg:text-4xl tracking-wide mb-3 lg:mb-4">
                    {{ $noUsers ? 'System Setup' : 'Welcome back' }}
                </h1>
                <p class="text-gray-400 text-sm sm:text-base leading-relaxed font-light px-2">
                    {{ $noUsers ? 'Create your administrator account to get started with managing your website' : 'Sign in to access your dashboard' }}
                </p>
            </div>
            
            @if(session('error'))
                <div class="p-3 lg:p-4 bg-red-500/10 border border-red-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-red-200">{{ session('error') }}</p>
                </div>
            @endif
            
            @if(session('success'))
                <div class="p-3 lg:p-4 bg-green-500/10 border border-green-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-green-200">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="p-3 lg:p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-yellow-200">{{ session('warning') }}</p>
                </div>
            @endif

            @if($noUsers)
                <form action="{{ route('admin.register') }}" method="POST" class="space-y-5 lg:space-y-6">
                    @csrf
                    
                    <div class="space-y-3 lg:space-y-4">
                        <div>
                            <input id="name" name="name" type="text" required 
                                   class="w-full px-4 lg:px-5 py-3 lg:py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Full Name" value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="email" name="email" type="email" required 
                                   class="w-full px-4 lg:px-5 py-3 lg:py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Email or Username" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="password" name="password" type="password" required 
                                   class="w-full px-4 lg:px-5 py-3 lg:py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Password">
                            @error('password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required 
                                   class="w-full px-4 lg:px-5 py-3 lg:py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Confirm Password">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 lg:py-4 px-6 text-white font-medium bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                        Create Account
                    </button>
                    
                    <div class="text-center pt-3 lg:pt-4">
                        <div class="px-3 lg:px-4 py-2 lg:py-2 bg-blue-500/10 border border-blue-500/20 rounded-lg backdrop-blur-sm">
                            <p class="text-xs text-blue-200 font-light">
                                Next step: You'll configure Two-Factor Authentication for enhanced security
                            </p>
                        </div>
                    </div>
                </form>
            @else
                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5 lg:space-y-6">
                    @csrf
                    
                    <div class="space-y-3 lg:space-y-4">
                        <div>
                            <input id="email" name="email" type="email" required 
                                   class="w-full px-4 lg:px-5 py-3 lg:py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm"
                                   placeholder="Email or Username" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="password" name="password" type="password" required 
                                   class="w-full px-4 lg:px-5 py-3 lg:py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm"
                                   placeholder="Password">
                            @error('password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 lg:py-4 px-6 text-white font-medium bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                        Sign In
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-8 lg:py-16 bg-gradient-to-br from-gray-800 to-gray-700 relative">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5"></div>
        <div class="text-center max-w-lg relative z-10">
            <div class="mb-8 lg:mb-12">
                <blockquote class="text-gray-100 text-lg lg:text-xl leading-relaxed font-light mb-4 lg:mb-6 px-4">
                    "Setting up your admin panel for complete website management and control."
                </blockquote>
            </div>
            
            <div class="inline-flex items-center space-x-3 lg:space-x-4 px-4 lg:px-6 py-3 lg:py-4 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 lg:w-14 lg:h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <img src="{{ asset('images/slow.png') }}" alt="Slow Profile" class="w-10 h-10 lg:w-12 lg:h-12 rounded-full object-cover">
                    </div>
                </div>
                <div class="text-left">
                    <div class="text-white font-medium text-sm lg:text-base mb-1">Admin Panel</div>
                    <div class="text-gray-300 text-xs lg:text-sm font-light">by SlowWebDev</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
