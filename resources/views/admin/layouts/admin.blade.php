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
        <!-- Mobile Menu Button -->
        <button id="mobile-menu-button" class="fixed top-4 right-4 z-50 lg:hidden bg-gray-800 text-white p-3 rounded-lg shadow-lg">
            <i class="fas fa-bars" id="menu-icon"></i>
        </button>

        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed right-0 top-0 w-64 h-full bg-gray-800 border-l border-gray-700 transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-40">
            <!-- Header -->
            <div class="p-4 border-b border-gray-700 lg:hidden">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Admin Panel</h2>
                    <button id="close-sidebar" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors hover:bg-gray-700
                          {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-home w-6 mr-3"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- Pages Management Dropdown -->
                <div class="admin-dropdown">
                    <button class="admin-dropdown-trigger flex items-center w-full px-4 py-3 text-gray-300 rounded-lg transition-colors hover:bg-gray-700
                           {{ request()->routeIs('admin.pages.*') ? 'bg-gray-700 text-white' : '' }}" data-dropdown="pages">
                        <i class="fas fa-edit w-6 mr-3"></i>
                        <span>Pages Management</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200"></i>
                    </button>
                    <div class="admin-dropdown-content" id="dropdown-pages">
                        <a href="{{ route('admin.pages.home') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.pages.home') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-home w-4 mr-3"></i>
                            <span>Home Page</span>
                        </a>
                        <a href="{{ route('admin.pages.about') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.pages.about') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-info-circle w-4 mr-3"></i>
                            <span>About Page</span>
                        </a>
                        <a href="{{ route('admin.pages.contact') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.pages.contact') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-envelope w-4 mr-3"></i>
                            <span>Contact Page</span>
                        </a>
                        <hr class="border-gray-600 my-2 mx-4">
                        <div class="px-4 py-1 text-xs text-gray-500 uppercase tracking-wider">Preview Pages</div>
                        <a href="{{ route('home') }}" target="_blank" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4">
                            <i class="fas fa-home w-4 mr-3"></i>
                            <span>View Home</span>
                            <i class="fas fa-external-link-alt ml-auto text-xs"></i>
                        </a>
                        <a href="{{ route('about') }}" target="_blank" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4">
                            <i class="fas fa-info-circle w-4 mr-3"></i>
                            <span>View About</span>
                            <i class="fas fa-external-link-alt ml-auto text-xs"></i>
                        </a>
                        <a href="{{ route('contact.index') }}" target="_blank" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4">
                            <i class="fas fa-envelope w-4 mr-3"></i>
                            <span>View Contact</span>
                            <i class="fas fa-external-link-alt ml-auto text-xs"></i>
                        </a>
                    </div>
                </div>
                
                <a href="{{ route('admin.projects.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors hover:bg-gray-700
                          {{ request()->routeIs('admin.projects.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-building w-6 mr-3"></i>
                    <span>Projects</span>
                </a>
                
                <a href="{{ route('admin.media.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors hover:bg-gray-700
                          {{ request()->routeIs('admin.media.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-images w-6 mr-3"></i>
                    <span>Media</span>
                </a>
                
                <a href="{{ route('admin.contacts.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors hover:bg-gray-700
                          {{ request()->routeIs('admin.contacts.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-envelope w-6 mr-3"></i>
                    <span>Messages</span>
                </a>
                
                <a href="{{ route('admin.careers.index') }}" 
                   class="flex items-center px-4 py-3 text-gray-300 rounded-lg transition-colors hover:bg-gray-700
                          {{ request()->routeIs('admin.careers.*') ? 'bg-gray-700 text-white border-r-4 border-blue-500' : '' }}">
                    <i class="fas fa-briefcase w-6 mr-3"></i>
                    <span>Careers</span>
                </a>
            </nav>

            <!-- User Menu -->
            <div class="absolute bottom-0 w-full bg-gray-900/50 backdrop-blur-sm border-t border-gray-700">
                <div class="p-4">
                    <div class="flex items-center justify-between text-gray-300">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                <span class="text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500">Administrator</div>
                            </div>
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-gray-400 hover:text-white transition-colors rounded-lg hover:bg-gray-800" title="Logout">
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
