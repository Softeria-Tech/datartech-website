{{-- resources/views/frontend/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Laravel')) - Digital Library</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/frontend.css'])
    @stack('styles')
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="fixed inset-0 bg-white dark:bg-gray-900 z-50 flex items-center justify-center transition-opacity duration-300">
        <div class="w-16 h-16 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loading-spinner').style.opacity = '0';
                setTimeout(function() {
                    document.getElementById('loading-spinner').style.display = 'none';
                }, 300);
            }, 500);
        });
    </script>

    <!-- Navigation -->
    @include('frontend.layouts.partials.navigation')

    <!-- Page Header -->
    @hasSection('header')
        <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                @yield('header')
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="min-h-screen">
        {{ $slot }}
        @yield('content')
    </main>

    <!-- Footer -->
    @include('frontend.layouts.partials.footer')

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    @stack('scripts')
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Toast Notifications -->
    <script>
        window.addEventListener('show-notification', event => {
            const notification = event.detail;
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-500 translate-x-full ${notification.type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
            toast.innerHTML = notification.message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        });
    </script>
</body>
</html>