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

            {{-- BUG-08 Fix: Temporary password display (one-time, not in generic flash) --}}
            @if(session('temp_password'))
                <div class="mb-5 rounded-xl border border-amber-300 bg-amber-50 p-4" x-data="{ copied: false }">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-amber-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-800">Temporary Password for {{ session('temp_password_user') }}</p>
                            <p class="mt-1 text-xs text-amber-700">Share this password securely with the user. It will not be shown again.</p>
                            <div class="mt-2 flex items-center gap-2">
                                <code class="rounded bg-white px-3 py-1.5 text-sm font-mono font-bold text-amber-900 border border-amber-200 select-all">{{ session('temp_password') }}</code>
                                <button @click="navigator.clipboard.writeText('{{ session('temp_password') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-700 transition-colors"
                                        x-text="copied ? 'Copied!' : 'Copy'">
                                    Copy
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-amber-600">⚠ The user must change this password upon first login.</p>
                        </div>
                    </div>
                </div>
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
