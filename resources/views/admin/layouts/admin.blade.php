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
        <button id="mobile-menu-button" class="fixed top-4 right-4 z-50 md:hidden bg-gray-800/90 backdrop-blur-sm text-white p-3 rounded-lg shadow-lg border border-gray-600">
            <i class="fas fa-bars" id="menu-icon"></i>
        </button>

        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 md:hidden hidden transition-opacity duration-300"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed right-0 top-0 w-64 h-full bg-gray-800 border-l border-gray-700 transform translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col">
            <!-- Fixed Header -->
            <div class="flex-shrink-0 p-4 border-b border-gray-700 md:hidden">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">
                        Admin Panel
                    </h2>
                    <button id="close-sidebar" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Desktop Fixed Header -->
            <div class="hidden md:block flex-shrink-0 p-4 border-b border-gray-700">
                <h2 class="text-lg font-semibold text-white">
                    Admin Panel
                </h2>
            </div>

            <!-- Scrollable Navigation -->
            <nav class="flex-1 overflow-y-auto mt-4 px-3 space-y-1 pb-4">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3 py-3 mx-1 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                          {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-home w-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                <!-- Pages Management Dropdown -->
                <div class="admin-dropdown mx-1">
                    <button class="admin-dropdown-trigger flex items-center w-full px-3 py-3 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                           {{ request()->routeIs('admin.pages.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}" data-dropdown="pages">
                        <i class="fas fa-edit w-5 mr-3 {{ request()->routeIs('admin.pages.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                        <span class="font-medium">Pages Management</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 text-xs"></i>
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
                   class="flex items-center px-3 py-3 mx-1 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                          {{ request()->routeIs('admin.projects.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-building w-5 mr-3 {{ request()->routeIs('admin.projects.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    <span class="font-medium">Projects</span>
                </a>
                
                <a href="{{ route('admin.media.index') }}" 
                   class="flex items-center px-3 py-3 mx-1 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                          {{ request()->routeIs('admin.media.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-images w-5 mr-3 {{ request()->routeIs('admin.media.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    <span class="font-medium">Media</span>
                </a>
                
                <a href="{{ route('admin.contacts.index') }}" 
                   class="flex items-center px-3 py-3 mx-1 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                          {{ request()->routeIs('admin.contacts.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-envelope w-5 mr-3 {{ request()->routeIs('admin.contacts.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    <span class="font-medium">Messages</span>
                </a>
                
                <a href="{{ route('admin.careers.index') }}" 
                   class="flex items-center px-3 py-3 mx-1 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                          {{ request()->routeIs('admin.careers.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-briefcase w-5 mr-3 {{ request()->routeIs('admin.careers.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    <span class="font-medium">Careers</span>
                </a>
                
                <a href="{{ route('admin.seo.unified') }}" 
                   class="flex items-center px-3 py-3 mx-1 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                          {{ request()->routeIs('admin.seo.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}">
                    <i class="fas fa-search w-5 mr-3 {{ request()->routeIs('admin.seo.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                    <span class="font-medium">SEO Manager</span>
                </a>
                
                <!-- Security Dropdown -->
                <div class="admin-dropdown mx-1">
                    <button class="admin-dropdown-trigger flex items-center w-full px-3 py-3 text-gray-300 rounded-lg transition-all hover:bg-gray-700 hover:text-white group
                           {{ request()->routeIs('admin.security.*') ? 'bg-blue-600 text-white shadow-lg' : '' }}" data-dropdown="security">
                        <i class="fas fa-shield-alt w-5 mr-3 {{ request()->routeIs('admin.security.*') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                        <span class="font-medium">Security</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 text-xs"></i>
                    </button>
                    <div class="admin-dropdown-content" id="dropdown-security">
                        <a href="{{ route('admin.security.overview') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.security.overview') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-shield-alt w-4 mr-3"></i>
                            <span>Security Center</span>
                        </a>
                        <a href="{{ route('admin.security.device-management') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.security.device-management') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-laptop w-4 mr-3"></i>
                            <span >Devices</span>
                        </a>
                        <a href="{{ route('admin.security.activity-logs') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.security.activity-logs') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-history w-4 mr-3"></i>
                            <span>Activity Logs</span>
                        </a>
                        <a href="{{ route('admin.security.security-events') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.security.security-events') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-exclamation-triangle w-4 mr-3"></i>
                            <span>Security Events</span>
                        </a>
                        @if(auth()->id() === 1)
                        <a href="{{ route('admin.security.settings') }}" 
                           class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors ml-4
                                  {{ request()->routeIs('admin.security.settings') ? 'bg-gray-600 text-white' : '' }}">
                            <i class="fas fa-cog w-4 mr-3"></i>
                            <span>Security Settings</span>
                        </a>
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Fixed User Menu -->
            <div class="flex-shrink-0 bg-gray-900 border-t border-gray-700">
                <div class="p-4">
                    <div class="flex items-center justify-between text-gray-300">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                                <span class="text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <div class="hidden md:block">
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