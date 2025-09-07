@extends('admin.layouts.auth')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    @php
        $noUsers = \App\Models\User::count() === 0;
    @endphp
    
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-8 py-16 bg-auth-left">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <h1 class="text-white font-extralight text-3xl lg:text-4xl tracking-wide mb-4">
                    {{ $noUsers ? 'System Setup' : 'Welcome back' }}
                </h1>
                <p class="text-gray-400 text-base leading-relaxed font-light">
                    {{ $noUsers ? 'Create your administrator account to get started with managing your website' : 'Sign in to access your dashboard' }}
                </p>
            </div>
            
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-red-200">{{ session('error') }}</p>
                </div>
            @endif
            
            @if(session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-green-200">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-yellow-200">{{ $message }}</p>
                </div>
            @endif

            @if($noUsers)
                <form action="{{ route('admin.register') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <input id="name" name="name" type="text" required 
                                   class="w-full px-5 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Full Name" value="{{ old('name') }}">
                            @error('name')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="email" name="email" type="email" required 
                                   class="w-full px-5 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Email or Username" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="password" name="password" type="password" required 
                                   class="w-full px-5 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Password">
                            @error('password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required 
                                   class="w-full px-5 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm" 
                                   placeholder="Confirm Password">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 px-6 text-white font-medium bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                        Create Account
                    </button>
                    
                    <div class="text-center pt-4">
                        <div class="px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-lg backdrop-blur-sm">
                            <p class="text-xs text-blue-200 font-light">
                                Next step: You'll configure Two-Factor Authentication for enhanced security
                            </p>
                        </div>
                    </div>
                </form>
            @else
                <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <input id="email" name="email" type="email" required 
                                   class="w-full px-5 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm"
                                   placeholder="Email or Username" value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <input id="password" name="password" type="password" required 
                                   class="w-full px-5 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-sm backdrop-blur-sm"
                                   placeholder="Password">
                            @error('password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 px-6 text-white font-medium bg-blue-600 hover:bg-blue-700 active:bg-blue-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                        Sign In
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-8 py-16 bg-auth-right relative">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5"></div>
        <div class="text-center max-w-lg relative z-10">
            <div class="mb-12">
                <blockquote class="text-gray-100 text-xl leading-relaxed font-light mb-6">
                    "Setting up your admin panel for complete website management and control."
                </blockquote>
            </div>
            
            <div class="inline-flex items-center space-x-4 px-6 py-4 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <img src="{{ asset('images/slow.png') }}" alt="Slow Profile" class="w-12 h-12 rounded-full object-cover">
                    </div>
                </div>
                <div class="text-left">
                    <div class="text-white font-medium text-base mb-1">Admin Panel</div>
                    <div class="text-gray-300 text-sm font-light">by SlowWebDev</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
