<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-primary rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="hidden sm:flex font-bold text-xl text-gray-900 dark:text-white">Datartech</span>
                </a>
            </div>

            <!-- Desktop Navigation Links (Hidden on Mobile) -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-600' : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400' }} px-3 py-2 text-sm font-medium transition">
                    Home
                </a>
                <a href="{{ route('library.resources') }}" 
                   class="{{ request()->routeIs('library.*') ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-600' : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400' }} px-3 py-2 text-sm font-medium transition">
                    Resources
                </a>
                <a href="{{ route('membership.plans') }}" 
                   class="{{ request()->routeIs('membership.*') ? 'text-primary-600 dark:text-primary-400 border-b-2 border-primary-600' : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400' }} px-3 py-2 text-sm font-medium transition">
                    Memberships
                </a>
                <a href="#" 
                   class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 text-sm font-medium transition">
                    Support
                </a>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <button id="theme-toggle" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>

                @auth                    
                    <!-- User Menu (Desktop) -->
                    <div class="hidden md:flex relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" 
                                     alt="{{ Auth::user()->name }}"
                                     class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-8 h-8 bg-gradient-primary rounded-full flex items-center justify-center text-white font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span class="hidden lg:inline text-sm text-gray-700 dark:text-gray-300">
                                {{ Auth::user()->name }}
                            </span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" 
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
                             x-transition>
                            @if (auth()->user()->isAdmin())
                                <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Admin Dashboard
                                </a>
                                <hr class="my-1 border-gray-200 dark:border-gray-700"/>
                            @endif
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Dashboard
                            </a>
                            <a href="{{ route('downloads.history') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                My Resources
                            </a>
                            <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                My Orders
                            </a>
                            <hr class="my-1 border-gray-200 dark:border-gray-700"/>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile User Menu Button -->
                    <div class="md:hidden">
                        <button id="mobile-user-menu-button" class="flex items-center focus:outline-none">
                            @if(Auth::user()->avatar)
                                <img src="{{ Storage::url(Auth::user()->avatar) }}" 
                                     alt="{{ Auth::user()->name }}"
                                     class="w-8 h-8 rounded-full object-cover border-2 border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-8 h-8 bg-gradient-primary rounded-full flex items-center justify-center text-white font-medium">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </button>
                    </div>
                @else
                    <!-- Desktop Auth Links -->
                    <div class="hidden md:flex items-center space-x-2">
                        <a href="{{ route('login') }}" 
                           class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 text-sm font-medium transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" 
                           class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                            Sign Up
                        </a>
                    </div>

                    <!-- Mobile Auth Button -->
                    <div class="md:hidden">
                        <a href="{{ route('login') }}" 
                           class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition">
                            Login
                        </a>
                    </div>
                @endauth

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (Navigation Links) -->
    <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" 
               class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                Home
            </a>
            <a href="{{ route('library.resources') }}" 
               class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('library.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                Resources
            </a>
            <a href="{{ route('membership.plans') }}" 
               class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('membership.*') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                Memberships
            </a>
            <a href="#" 
               class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Support
            </a>
        </div>

        @auth
            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 pb-3">
                <div class="px-2 space-y-1">
                    @if (auth()->user()->isAdmin())
                        <a href="/admin" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Admin Dashboard
                        </a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Dashboard
                    </a>
                    <a href="{{ route('downloads.history') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        My Resources
                    </a>
                    <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        My Orders
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>

    <!-- Mobile User Menu (Profile Dropdown) -->
    @auth
    <div id="mobile-user-menu" class="hidden md:hidden fixed inset-0 z-50" x-data="{ open: false }" x-show="open" x-cloak>
        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" @click="open = false"></div>
        <div class="absolute right-4 top-16 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
            </div>
            <div class="py-1">
                @if (auth()->user()->isAdmin())
                    <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Admin Dashboard
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Dashboard
                </a>
                <a href="{{ route('downloads.history') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    My Resources
                </a>
                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    My Orders
                </a>
                <hr class="my-1 border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth
</nav>

<script>
    // Dark Mode Toggle
    const themeToggle = document.getElementById('theme-toggle');
    const html = document.documentElement;
    
    if (localStorage.getItem('theme') === 'dark') {
        html.classList.add('dark');
    }
    
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    }
    
    // Mobile Menu Toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileUserMenu = document.getElementById('mobile-user-menu');
    const mobileUserMenuButton = document.getElementById('mobile-user-menu-button');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
            // Close user menu if open
            if (mobileUserMenu && !mobileUserMenu.classList.contains('hidden')) {
                mobileUserMenu.classList.add('hidden');
            }
        });
    }
    
    // Mobile User Menu Toggle
    if (mobileUserMenuButton && mobileUserMenu) {
        mobileUserMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileUserMenu.classList.toggle('hidden');
            // Close main menu if open
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
            }
        });
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileUserMenu.contains(event.target) && !mobileUserMenuButton.contains(event.target)) {
                mobileUserMenu.classList.add('hidden');
            }
        });
    }
    
    // Close mobile menu when clicking a link
    const mobileLinks = document.querySelectorAll('#mobile-menu a, #mobile-menu button');
    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>