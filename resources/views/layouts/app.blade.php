<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BestBuddies') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="font-sans antialiased"
    x-data="{
        sidebarOpen: window.innerWidth >= 768,
        sidebarExpanded: window.innerWidth >= 768,
        contentMargin: window.innerWidth >= 768 ? 'ml-[260px]' : 'ml-0'
    }"
    @resize.window="
        sidebarOpen = window.innerWidth >= 768;
        sidebarExpanded = window.innerWidth >= 768;
        contentMargin = window.innerWidth >= 768 ? (sidebarExpanded ? 'ml-[260px]' : 'ml-[80px]') : 'ml-0';
    "
    @toggle-sidebar.window="sidebarExpanded = !sidebarExpanded">
    
    <div class="h-full min-h-screen flex">
        <!-- Sidebar componente -->
        @include('components.sidebar', ['expanded' => 'sidebarExpanded'])
        
        <!-- Contenido principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar componente -->
            @include('components.navbar', [
                'title' => $title ?? 'Panel Principal',
                'userName' => $userName ?? null,
                'userRole' => $userRole ?? null
            ])
            
            <!-- Contenido del dashboard -->
            <main class="flex-1 overflow-y-auto bg-blue-50 p-2 ml-[5px]">
                @yield('content')
            </main>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>