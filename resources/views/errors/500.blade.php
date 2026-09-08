<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | Chemical Stock OS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 flex items-center justify-center p-4 font-inter text-slate-100">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-16 h-16 bg-red-500/20 border border-red-500/30 rounded-2xl mx-auto flex items-center justify-center text-red-400">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-4xl font-extrabold tracking-tight text-white">500</h1>
            <p class="text-lg font-semibold text-slate-200 mt-2">Internal Server Error</p>
            <p class="text-sm text-slate-400 mt-1">An unexpected error occurred while processing your request. The incident has been recorded in the system audit logs.</p>
        </div>
        <div class="pt-2 flex justify-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                Return to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
