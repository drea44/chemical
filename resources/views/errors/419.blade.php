<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 — Page Expired | Chemical Stock OS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 flex items-center justify-center p-4 font-inter text-slate-100">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-16 h-16 bg-yellow-500/20 border border-yellow-500/30 rounded-2xl mx-auto flex items-center justify-center text-yellow-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-white">419</h1>
            <p class="text-lg font-semibold text-slate-200 mt-2">Session Expired</p>
            <p class="text-sm text-slate-400 mt-1">Your session or security CSRF token has expired due to inactivity. Please refresh or log in again.</p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="{{ route('login') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                Log In Again
            </a>
        </div>
    </div>
</body>
</html>
