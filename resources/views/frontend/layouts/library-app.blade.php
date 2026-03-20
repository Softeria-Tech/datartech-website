{{-- resources/views/frontend/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Laravel')) - Datartech</title>
    
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
    

    <a href="https://wa.me/254726238623?text=Hello%2C%20I%27m%20interested%20in%20your%20services" class="whatsapp-float" target="_blank" rel="noopener noreferrer"
        aria-label="Chat with us on WhatsApp">
        <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.087-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824z"/>
        </svg>
        <span class="tooltip-text">+254726238623</span>
    </a>

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
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('trigger-download', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>
</body>
</html>