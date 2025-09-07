@extends('admin.layouts.auth')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-8 py-16 bg-auth-left">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <h1 class="text-white font-extralight text-3xl lg:text-4xl tracking-wide mb-4">
                    Enter Verification Code
                </h1>
                <p class="text-gray-400 text-base leading-relaxed font-light">
                    Check your authenticator app for the 6-digit code
                </p>
            </div>
            
            @if(session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-green-200">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-red-200">{{ session('error') }}</p>
                </div>
            @endif
            
            @if(session('warning'))
                <div class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-yellow-200">{{ session('warning') }}</p>
                </div>
            @endif

            <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg backdrop-blur-sm">
                <p class="text-sm text-blue-200">
                    Open your authenticator app to get the current 6-digit code
                </p>
            </div>

            <form method="POST" action="{{ route('admin.2fa.process-verify') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="code" class="block text-base font-medium text-gray-300 mb-3">
                        Authentication Code:
                    </label>
                    <input type="text" id="code" name="code" 
                           class="w-full px-6 py-4 text-white placeholder-gray-400 bg-transparent border border-white/20 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-400 transition-all duration-300 text-center text-2xl font-mono tracking-widest backdrop-blur-sm"
                           placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required autofocus>
                    
                    @error('code')
                        <p class="mt-2 text-xs text-red-300">
                            {{ $message }}
                        </p>
                    @enderror
                    
                    <div class="mt-3 flex items-center justify-center space-x-2 text-xs text-gray-400">
                        <div class="w-4 h-4 bg-current rounded-full opacity-75"></div>
                        <span>Code refreshes every 30 seconds</span>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 px-6 text-white font-medium bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 active:from-green-800 active:to-green-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500/50 transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                    Verify & Continue
                </button>
            </form>

            <div class="text-center">
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-200 transition-colors px-4 py-2 rounded-lg hover:bg-white/5">
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-8 py-16 bg-auth-right relative">
        <div class="absolute inset-0 bg-gradient-to-br from-green-600/5 to-blue-600/5"></div>
        <div class="text-center max-w-lg relative z-10">
            <div class="mb-12">
                <h3 class="text-2xl font-light text-white mb-6">Check Your Phone</h3>
                <p class="text-gray-300 text-base leading-relaxed font-light">
                    Open your authenticator app (Google Authenticator, Authy, etc.) and enter the 6-digit code displayed for this account.
                </p>
            </div>
            
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-sm mb-8">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-green-400 rounded-full flex-shrink-0 mt-0.5"></div>
                    <div>
                        <h4 class="text-white font-medium mb-2 text-sm">Security Tip:</h4>
                        <p class="text-gray-300 text-sm leading-relaxed">
                            The code changes every 30 seconds for maximum security. If the code doesn't work, wait for a new one to generate.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="inline-flex items-center space-x-4 px-6 py-4 bg-white/5 border border-white/10 rounded-2xl backdrop-blur-sm">
                <div class="flex-shrink-0">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                        <img src="{{ asset('images/slow.png') }}" alt="Slow Profile" class="w-12 h-12 rounded-full object-cover">
                    </div>
                </div>
                <div class="text-left">
                    <div class="text-white font-medium text-base mb-1">2FA Verification</div>
                    <div class="text-gray-300 text-sm font-light">by SlowWebDev</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
