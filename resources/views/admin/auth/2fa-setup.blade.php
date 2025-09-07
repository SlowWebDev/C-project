@extends('admin.layouts.auth')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div class="min-h-screen flex flex-col lg:flex-row">
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-8 py-16 bg-auth-left">
        <div class="w-full max-w-lg space-y-8">
            <div class="text-center">
                <h1 class="text-white font-extralight text-3xl lg:text-4xl tracking-wide mb-4">
                    Setup Two-Factor Authentication
                </h1>
                <p class="text-gray-400 text-base leading-relaxed font-light">
                    Add extra security to your account by enabling 2FA
                </p>
            </div>
            
            @if(session('success'))
                <div class="p-3 bg-green-500/10 border border-green-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-green-200">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="p-3 bg-red-500/10 border border-red-500/30 rounded-lg backdrop-blur-sm">
                    <p class="text-sm text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
                <h2 class="text-lg font-medium text-white mb-4">Instructions:</h2>
                <ol class="text-sm text-gray-300 space-y-3">
                    <li class="flex items-start">
                        <span class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0 shadow-lg font-medium">1</span>
                        <span class="leading-relaxed">Install Google Authenticator or Authy on your phone</span>
                    </li>
                    <li class="flex items-start">
                        <span class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0 shadow-lg font-medium">2</span>
                        <span class="leading-relaxed">Scan the QR code shown on the right side</span>
                    </li>
                    <li class="flex items-start">
                        <span class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs mr-3 mt-0.5 flex-shrink-0 shadow-lg font-medium">3</span>
                        <span class="leading-relaxed">Enter the 6-digit code from your app below</span>
                    </li>
                </ol>
            </div>

            <form method="POST" action="{{ route('admin.2fa.verify') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label for="code" class="block text-base font-medium text-gray-300 mb-3">
                        Enter 6-digit code from your app:
                    </label>
                    <input type="text" id="code" name="code" 
                           class="w-full px-6 py-4 text-white placeholder-gray-400 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 transition-all duration-300 text-center text-2xl font-mono tracking-widest backdrop-blur-sm"
                           placeholder="000000" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" required autofocus>
                    
                    @error('code')
                        <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-4 px-6 text-white font-medium bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 active:from-blue-800 active:to-blue-900 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300 transform hover:scale-[0.99] shadow-lg">
                    Enable Two-Factor Authentication
                </button>
            </form>

            <div class="text-center">
                <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-400 hover:text-gray-200 transition-colors px-4 py-2 rounded-lg hover:bg-white/5">
                        Cancel & Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="flex-1 lg:flex-none lg:w-1/2 flex items-center justify-center px-8 py-16 bg-auth-right relative">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-purple-600/5"></div>
        <div class="text-center max-w-lg w-full relative z-10">
            
            <h3 class="text-2xl font-light text-white mb-16">Scan QR Code</h3>
            
            <div class="mb-16">
                <div class="bg-white p-6 rounded-3xl shadow-2xl inline-block">
                    @if(!empty($qrCodeImage))
                        <img src="{{ $qrCodeImage }}" alt="2FA QR Code" class="w-56 h-56 block">
                    @elseif(!empty($qrCodeBackup))
                        <img src="{{ $qrCodeBackup }}" alt="2FA QR Code (Backup)" class="w-56 h-56 block">
                    @else
                        <div class="w-56 h-56 border-2 border-dashed border-gray-300 rounded-2xl flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <div class="w-12 h-12 bg-gray-400 rounded-lg mx-auto mb-3"></div>
                                <p class="text-sm">QR Code Unavailable</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            @if(!empty($secret))
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-sm">
                    <h4 class="text-white font-medium mb-4 text-center">Manual Setup Key</h4>
                    <div class="bg-gray-900/50 p-4 rounded-xl border border-gray-600/50">
                        <code class="text-gray-100 font-mono text-sm break-all leading-relaxed block text-center">{{ $secret }}</code>
                    </div>
                    <p class="text-gray-400 text-xs mt-4 text-center">Copy this key if you can't scan the QR code</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection