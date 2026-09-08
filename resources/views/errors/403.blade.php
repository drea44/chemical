<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Forbidden | Chemical Stock OS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 flex items-center justify-center p-4 font-inter text-slate-100">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-16 h-16 bg-amber-500/20 border border-amber-500/30 rounded-2xl mx-auto flex items-center justify-center text-amber-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-white">403</h1>
            <p class="text-lg font-semibold text-slate-200 mt-2">Access Forbidden</p>
            <p class="text-sm text-slate-400 mt-1">You do not have permission or role clearance to access this resource or perform this action.</p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
               class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg text-sm font-medium transition-colors border border-slate-700">
                Go Back
            </a>
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                Dashboard
            </a>
        </div>
    </div>
</body>
</html>
