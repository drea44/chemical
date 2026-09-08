<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Chemical Stock OS</title>
    <meta name="description" content="Chemical Stock OS — Enterprise chemical inventory management system">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-inter antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" @resize.window="if (window.innerWidth >= 1024) { sidebarOpen = true }">

<div class="min-h-screen flex flex-col">
    <!-- Backdrop for mobile sidebar drawer -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
    </div>

    <!-- Sidebar -->
    <x-sidebar />

    <!-- Main Content -->
    <div class="flex-1 flex flex-col transition-all duration-200 ease-in-out min-w-0"
         :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">

        <!-- Topbar -->
        <x-topbar :title="$title ?? 'Dashboard'" :breadcrumb="$breadcrumb ?? []" />

        <!-- Page Content -->
        <main class="flex-1 p-4 sm:p-6 w-full max-w-full overflow-x-hidden">
            <!-- Flash Messages -->
            @if(session('success'))
                <x-alert type="success" :message="session('success')" class="mb-5" />
            @endif
            @if(session('error'))
                <x-alert type="error" :message="session('error')" class="mb-5" />
            @endif
            @if(session('warning'))
                <x-alert type="warning" :message="session('warning')" class="mb-5" />
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="px-4 sm:px-6 py-3 border-t border-gray-200 bg-white">
            <p class="text-xs text-gray-400">Chemical Stock OS &copy; {{ date('Y') }} — Enterprise Chemical Inventory Management</p>
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
