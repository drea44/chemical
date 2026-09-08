<?php $__env->startSection('title', 'Sign In'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex h-screen">

    <!-- LEFT: Login Form -->
    <div class="w-full lg:w-5/12 flex flex-col justify-center px-8 sm:px-16 lg:px-20 bg-white">
        <div class="max-w-sm mx-auto w-full">

            <!-- Logo -->
            <div class="flex items-center gap-3 mb-10">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background:#2563eb;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-900 font-bold text-base leading-tight">Chemical Stock OS</p>
                    <p class="text-gray-400 text-xs">Enterprise Edition</p>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Sign in to your account</h1>
            <p class="text-sm text-gray-500 mb-8">Enter your credentials to access the system.</p>

            <?php if($errors->any()): ?>
            <div class="mb-5 p-3 rounded-lg bg-red-50 border border-red-200">
                <p class="text-sm text-red-700 font-medium"><?php echo e($errors->first()); ?></p>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div class="space-y-1">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        autofocus
                        value="<?php echo e(old('email')); ?>"
                        placeholder="you@example.com"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors <?php echo e($errors->has('email') ? 'border-red-300 bg-red-50' : ''); ?>"
                    >
                </div>

                <div class="space-y-1">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            placeholder="••••••••"
                            class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors pr-10"
                        >
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors duration-150 flex items-center justify-center gap-2"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Sign In
                </button>
            </form>

            <!-- Dev credentials note -->
            <div class="mt-8 p-4 rounded-lg border border-gray-200 bg-gray-50">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Development Accounts</p>
                <div class="space-y-1 text-xs text-gray-500">
                    <p><span class="font-medium text-gray-700">admin@example.com</span> — Administrator</p>
                    <p><span class="font-medium text-gray-700">manager@example.com</span> — Stock Manager</p>
                    <p><span class="font-medium text-gray-700">auditor@example.com</span> — Auditor</p>
                    <p><span class="font-medium text-gray-700">viewer@example.com</span> — Viewer</p>
                    <p class="mt-2 text-gray-400">Password for all: <span class="font-mono font-medium text-gray-600">password</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Dark Navy Panel -->
    <div class="hidden lg:flex lg:w-7/12 flex-col justify-between p-16" style="background:#0f172a;">

        <!-- Molecular SVG Illustration -->
        <div class="flex-1 flex items-center justify-center">
            <svg width="420" height="360" viewBox="0 0 420 360" fill="none" xmlns="http://www.w3.org/2000/svg" opacity="0.85">
                <!-- Atoms (circles) -->
                <circle cx="210" cy="180" r="18" fill="#2563eb" opacity="0.9"/>
                <circle cx="120" cy="110" r="13" fill="#3b82f6" opacity="0.7"/>
                <circle cx="310" cy="100" r="16" fill="#1d4ed8" opacity="0.8"/>
                <circle cx="80"  cy="240" r="11" fill="#60a5fa" opacity="0.6"/>
                <circle cx="320" cy="260" r="14" fill="#3b82f6" opacity="0.7"/>
                <circle cx="170" cy="290" r="10" fill="#93c5fd" opacity="0.5"/>
                <circle cx="360" cy="180" r="12" fill="#2563eb" opacity="0.65"/>
                <circle cx="60"  cy="150" r="9"  fill="#60a5fa" opacity="0.5"/>
                <circle cx="260" cy="310" r="8"  fill="#93c5fd" opacity="0.45"/>
                <circle cx="150" cy="50"  r="10" fill="#3b82f6" opacity="0.55"/>
                <circle cx="280" cy="50"  r="8"  fill="#60a5fa" opacity="0.5"/>

                <!-- Bonds (lines) -->
                <line x1="210" y1="180" x2="120" y2="110" stroke="#3b82f6" stroke-width="2" opacity="0.5"/>
                <line x1="210" y1="180" x2="310" y2="100" stroke="#3b82f6" stroke-width="2" opacity="0.5"/>
                <line x1="210" y1="180" x2="80"  y2="240" stroke="#60a5fa" stroke-width="1.5" opacity="0.4"/>
                <line x1="210" y1="180" x2="320" y2="260" stroke="#3b82f6" stroke-width="2" opacity="0.4"/>
                <line x1="210" y1="180" x2="170" y2="290" stroke="#60a5fa" stroke-width="1.5" opacity="0.35"/>
                <line x1="210" y1="180" x2="360" y2="180" stroke="#2563eb" stroke-width="2" opacity="0.45"/>
                <line x1="120" y1="110" x2="60"  y2="150" stroke="#60a5fa" stroke-width="1.5" opacity="0.35"/>
                <line x1="120" y1="110" x2="150" y2="50"  stroke="#3b82f6" stroke-width="1.5" opacity="0.35"/>
                <line x1="310" y1="100" x2="280" y2="50"  stroke="#3b82f6" stroke-width="1.5" opacity="0.35"/>
                <line x1="310" y1="100" x2="360" y2="180" stroke="#1d4ed8" stroke-width="1.5" opacity="0.4"/>
                <line x1="320" y1="260" x2="260" y2="310" stroke="#3b82f6" stroke-width="1.5" opacity="0.35"/>
                <line x1="80"  y1="240" x2="170" y2="290" stroke="#60a5fa" stroke-width="1"   opacity="0.3"/>

                <!-- Atom labels -->
                <text x="210" y="185" text-anchor="middle" font-family="monospace" font-size="10" fill="white" font-weight="600">C</text>
                <text x="120" y="115" text-anchor="middle" font-family="monospace" font-size="8"  fill="#93c5fd">H</text>
                <text x="310" y="105" text-anchor="middle" font-family="monospace" font-size="9"  fill="#93c5fd">O</text>
                <text x="80"  y="244" text-anchor="middle" font-family="monospace" font-size="8"  fill="#bfdbfe">N</text>
                <text x="320" y="264" text-anchor="middle" font-family="monospace" font-size="8"  fill="#93c5fd">Cl</text>
                <text x="360" y="184" text-anchor="middle" font-family="monospace" font-size="8"  fill="#bfdbfe">S</text>
            </svg>
        </div>

        <!-- Text -->
        <div class="max-w-md">
            <h2 class="text-3xl font-bold text-white mb-4 leading-snug">
                A comprehensive, digital-first operating system for critical chemical stock management.
            </h2>
            <p class="text-sm leading-relaxed" style="color:rgba(255,255,255,0.5);">
                Real-time inventory tracking · QR code scanning · Compliance reporting · Full audit trail · Multi-role access control
            </p>
            <div class="mt-8 flex gap-6">
                <div>
                    <p class="text-2xl font-bold text-white">22+</p>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4);">Chemical Types</p>
                </div>
                <div class="w-px bg-white opacity-10"></div>
                <div>
                    <p class="text-2xl font-bold text-white">50+</p>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4);">Transactions Tracked</p>
                </div>
                <div class="w-px bg-white opacity-10"></div>
                <div>
                    <p class="text-2xl font-bold text-white">4</p>
                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.4);">Access Roles</p>
                </div>
            </div>
        </div>

        <p class="text-xs mt-8" style="color:rgba(255,255,255,0.2);">Chemical Stock OS &copy; <?php echo e(date('Y')); ?> — Enterprise Edition</p>
    </div>
</div>

<script>
function togglePassword() {
    const pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/auth/login.blade.php ENDPATH**/ ?>