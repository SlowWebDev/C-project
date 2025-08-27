<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/css/admin.css', 'resources/js/admin.js'])
<body class="bg-gray-900">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="fixed right-0 top-0 w-64 h-full bg-gray-800 border-l border-gray-700">
    
            <!-- Navigation -->
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors
                          {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-home w-6"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.projects.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors
                          {{ request()->routeIs('admin.projects.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-building w-6"></i>
                    <span>Projects</span>
                </a>
                
                <a href="{{ route('admin.media.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors
                          {{ request()->routeIs('admin.media.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-images w-6"></i>
                    <span>Media</span>
                </a>
                
                <a href="{{ route('admin.contacts.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors
                          {{ request()->routeIs('admin.contacts.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-envelope w-6"></i>
                    <span>Messages</span>
                </a>
                
                <a href="{{ route('admin.careers.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors
                          {{ request()->routeIs('admin.careers.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-briefcase w-6"></i>
                    <span>Careers</span>
                </a>
            </nav>

            <!-- User Menu -->
            <div class="absolute bottom-0 w-full bg-gray-900/50 backdrop-blur-sm border-t border-gray-700">
                <div class="p-4">
                    <div class="flex items-center justify-between text-gray-300">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white">
                                <span class="text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">Administrator</div>
                            </div>
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-white transition-colors rounded-lg hover:bg-gray-800">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main-content">
            <!-- Content -->
            <main class="admin-content-area">

                <!-- Alert Messages -->
                @if (session('success'))
                    <div class="mb-4 px-6 py-4 bg-green-900/20 backdrop-blur-sm border border-green-500/50 text-green-400 rounded-xl flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 px-6 py-4 bg-red-900/20 backdrop-blur-sm border border-red-500/50 text-red-400 rounded-xl flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 px-6 py-4 bg-amber-900/20 backdrop-blur-sm border border-amber-500/50 text-amber-400 rounded-xl flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 px-6 py-4 bg-blue-900/20 backdrop-blur-sm border border-blue-500/50 text-blue-400 rounded-xl flex items-center">
                        <i class="fas fa-info-circle mr-3"></i>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif

                <!-- Main Content -->
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-800 bg-gray-900/50 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto py-4 px-8">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Dashboard made by <a href="https://github.com/SlowWebDev" target="_blank" class="text-white">1slow</a>
                        </p>
                        <div class="flex items-center gap-4">
                            <a href="https://github.com/SlowWebDev" target="_blank" class="text-gray-500 hover:text-white transition-colors">
                                <i class="fab fa-github"></i>
                            </a>
<a href="https://discord.com/users/901116476105314374" target="_blank" class="text-gray-500 hover:text-white transition-colors">
    <i class="fab fa-discord"></i>
</a>

                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
