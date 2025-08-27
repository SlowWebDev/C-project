<!-- Main Navigation -->
<nav class="absolute top-0 left-0 right-0 z-50 pt-2 sm:pt-3 md:pt-4">
    <div class="container mx-auto px-3 sm:px-4 md:px-5">
        <div class="flex items-center justify-between">
            <!-- Logo -->
<div class="flex-shrink-0 pl-4">
    <a href="{{ route('home') }}" class="flex items-center">
<img src="{{ \App\Models\Setting::getLogo('logo') }}" class="site-logo" data-type="logo" 
     class="h-24 w-auto" 
     alt="Company Logo"
     fetchpriority="high"
     decoding="async"
     width="96"
     height="96">
    </a>
</div>
          <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center">
                <div class="flex items-center">
                    <div class="flex items-center space-x-1.5 rounded-full border border-white/20 px-3 py-1.5">
                        <a href="{{ route('home') }}" 
                           class="nav-desktop nav-link {{ request()->routeIs('home') ? 'nav-active' : '' }}">
                            HOME
                        </a>
                        <a href="{{ route('about') }}" 
                           class="nav-desktop nav-link {{ request()->routeIs('about') ? 'nav-active' : '' }}">
                            ABOUT US
                        </a>
                        <a href="{{ route('projects.index') }}" 
                           class="nav-desktop nav-link {{ request()->routeIs('projects.*') ? 'nav-active' : '' }}">
                            OUR PROJECTS
                        </a>
                        <a href="{{ route('media.index') }}" 
                           class="nav-desktop nav-link {{ request()->routeIs('media.*') ? 'nav-active' : '' }}">
                            MEDIA CENTER
                        </a>
                        <a href="{{ route('careers.index') }}" 
                           class="nav-desktop nav-link {{ request()->routeIs('careers.*') ? 'nav-active' : '' }}">
                            CAREERS
                        </a>
                        <a href="{{ route('contact.index') }}" 
                           class="nav-desktop nav-link {{ request()->routeIs('contact.*') ? 'nav-active' : '' }}">
                            CONTACT US
                        </a>
                    </div>
                </div>
            </div>

<!-- Phone Number -->
<div class="flex mt-2 lg:mt-0">
    <a href="tel:16497" 
       class="flex items-center gap-2 text-white rounded-full px-4 py-2 border border-white/10">
        <img src="{{ asset('assets/images/svg-icons/phone.svg') }}" class="w-5 h-5" alt="Phone">
        <span class="font-medium">16497</span>
    </a>
</div>

            <!-- Mobile Menu Button -->
            <div class="lg:hidden">
                <button class="mobile-menu-toggle p-2">
                    <img src="{{ asset('assets/images/svg-icons/menu.svg') }}" class="w-6 h-6" alt="Menu">
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay fixed inset-0 bg-black/50 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300 ease-in-out z-40"></div>
    
    <div class="mobile-menu fixed inset-y-0 right-0 w-[300px] bg-white shadow-xl transform translate-x-full transition-all duration-300 ease-in-out z-50">
        <!-- Header -->
        <div class="drawer-header">
            <div class="flex items-center justify-between">
                <div class="text-xl font-bold tracking-tight text-gray-900">TG</div>
                <button class="mobile-menu-close p-2 -m-2 text-gray-500" >
                    <img src="{{ asset('assets/images/svg-icons/close.svg') }}" class="w-5 h-5" alt="Close">
                </button>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="overflow-y-auto">
            <nav class="drawer-nav flex flex-col">
                <a href="{{ route('home') }}" 
                   class="drawer-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    HOME
                </a>
                <a href="{{ route('about') }}" 
                   class="drawer-item {{ request()->routeIs('about') ? 'active' : '' }}">
                    ABOUT US
                </a>
                <a href="{{ route('projects.index') }}" 
                   class="drawer-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    OUR PROJECTS
                </a>
                <a href="{{ route('media.index') }}" 
                   class="drawer-item {{ request()->routeIs('media.*') ? 'active' : '' }}">
                    MEDIA CENTER
                </a>
                <a href="{{ route('careers.index') }}" 
                   class="drawer-item {{ request()->routeIs('careers') ? 'active' : '' }}">
                    CAREERS
                </a>
                <a href="{{ route('contact.index') }}" 
                   class="drawer-item {{ request()->routeIs('contact') ? 'active' : '' }}">
                    CONTACT US
                </a>
            </nav>
        </div>
    </div>
</nav>