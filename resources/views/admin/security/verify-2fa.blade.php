@extends('admin.layouts.admin')

@section('title', 'Security Verification')

@section('content')
<div class="min-h-screen flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <div class="max-w-md w-full space-y-6 lg:space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 lg:h-20 lg:w-20 bg-blue-500/20 rounded-full flex items-center justify-center mb-4 lg:mb-6">
                <i class="fas fa-shield-alt text-blue-400 text-2xl lg:text-3xl"></i>
            </div>
            <h2 class="text-2xl lg:text-3xl font-bold text-white mb-2">Security Verification</h2>
            <p class="text-gray-400 text-sm lg:text-base px-4">Enter your 2FA code to proceed with {{ $actionText }}</p>
        </div>

        <!-- Device Info -->
        <div class="bg-white/5 rounded-xl p-4 border border-white/10 backdrop-blur-sm">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-laptop text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-white font-medium">{{ $deviceName }}</h3>
                    <p class="text-sm text-gray-400">Target device for {{ strtolower($actionText) }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-400 text-xs font-medium rounded-full border border-blue-500/30">
                        {{ $pendingAction === 'block_device' ? 'Block' : 'Unblock' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2FA Form -->
        <div class="bg-white/5 rounded-xl p-6 lg:p-8 border border-white/10 backdrop-blur-sm">
            <form method="POST" action="{{ route('admin.security.process-2fa') }}" class="space-y-6">
                @csrf
                
                <div>
                    @if(auth()->user()->hasTwoFactorEnabled())
                        <label for="tfa_code" class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-mobile-alt mr-2"></i>Authentication Code
                        </label>
                        
                        <input type="text" 
                               id="tfa_code" 
                               name="tfa_code" 
                               class="w-full px-4 py-3 lg:py-4 bg-white/5 border border-white/10 rounded-xl text-white text-center text-xl lg:text-2xl tracking-widest placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 backdrop-blur-sm @error('tfa_code') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror" 
                               placeholder="000000"
                               maxlength="6"
                               pattern="[0-9]{6}"
                               autocomplete="one-time-code"
                               inputmode="numeric"
                               required
                               autofocus>
                        
                        <p class="mt-2 text-xs text-gray-500 text-center">
                            Enter the 6-digit code from your authenticator app
                        </p>
                    @else
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-3">
                            <i class="fas fa-key mr-2"></i>Current Password
                        </label>
                        
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="w-full px-4 py-3 lg:py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 backdrop-blur-sm @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror" 
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               required
                               autofocus>
                        
                        <p class="mt-2 text-xs text-gray-500 text-center">
                            Enter your current password to confirm this security action
                        </p>
                        
                        <div class="mt-3 p-3 bg-blue-500/10 border border-blue-500/30 rounded-lg backdrop-blur-sm">
                            <p class="text-xs text-blue-300 text-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Two-factor authentication not set up. Using password verification as fallback.
                                <a href="{{ route('admin.2fa.setup') }}" class="underline hover:no-underline">Set up 2FA</a>
                            </p>
                        </div>
                    @endif
                    
                    @error('tfa_code')
                        <p class="mt-2 text-sm text-red-400">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                    
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">
                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('admin.security.device-management') }}" 
                       class="flex-1 bg-gray-600/80 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300 text-center backdrop-blur-sm">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    
                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                        <i class="fas fa-shield-alt mr-2"></i>Verify & {{ $pendingAction === 'block_device' ? 'Block' : 'Unblock' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Notice -->
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-300">
                        This security verification is required for all device management actions to protect your account.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
