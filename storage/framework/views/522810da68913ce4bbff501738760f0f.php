<?php
    $navItems = [
        ['label' => 'Overview',          'route' => 'dashboard',        'icon' => 'layout-dashboard', 'match' => 'dashboard'],
        ['label' => 'Chemical Registry', 'route' => 'chemicals.index',  'icon' => 'flask-conical',    'match' => 'chemicals*'],
        ['label' => 'Stock',             'route' => 'stock.index',      'icon' => 'package',          'match' => 'stock*'],
        ['label' => 'Transactions',      'route' => 'transactions.index','icon' => 'arrow-left-right', 'match' => 'transactions*'],
        ['label' => 'QR Scanner',        'route' => 'qr-scanner.index', 'icon' => 'scan-line',        'match' => 'qr-scanner*'],
        ['label' => 'Reports',           'route' => 'reports.index',    'icon' => 'bar-chart-2',      'match' => 'reports*'],
        ['label' => 'Audit Trail',       'route' => 'audit-trail.index','icon' => 'shield-check',     'match' => 'audit-trail*', 'roles' => ['ADMIN', 'AUDITOR']],
    ];
    $masterDataItems = [
        ['label' => 'Categories', 'route' => 'categories.index', 'icon' => 'tags',    'match' => 'categories*'],
        ['label' => 'Suppliers',  'route' => 'suppliers.index',  'icon' => 'truck',   'match' => 'suppliers*'],
        ['label' => 'Locations',  'route' => 'locations.index',  'icon' => 'map-pin', 'match' => 'locations*'],
    ];
    $mgmtItems = [
        ['label' => 'Users',    'route' => 'users.index',    'icon' => 'users',    'match' => 'users*',    'roles' => ['ADMIN']],
        ['label' => 'Settings', 'route' => 'settings.index', 'icon' => 'settings', 'match' => 'settings*', 'roles' => ['ADMIN']],
    ];
?>

<aside
    class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 text-white transition-transform duration-200 ease-in-out shadow-xl lg:shadow-none"
    style="background:#0f172a;"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <!-- Logo -->
    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.08);">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center" style="background:#2563eb;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-sm font-semibold leading-tight tracking-wide">Chemical Stock OS</p>
                <p class="text-xs leading-tight" style="color:rgba(255,255,255,0.4);">v1.0.0 Enterprise</p>
            </div>
        </div>
        <!-- Close button on mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded text-gray-400 hover:text-white hover:bg-slate-800 transition-colors" aria-label="Close menu">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <!-- MAIN section -->
        <p class="px-2 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,0.3);">Main</p>

        <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $allowed = !isset($item['roles']) || in_array(auth()->user()->role, $item['roles']);
                $active  = request()->routeIs($item['match']);
            ?>
            <?php if($allowed): ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium transition-colors duration-150 group"
               style="<?php echo e($active ? 'background:rgba(37,99,235,0.25); color:#60a5fa;' : 'color:rgba(255,255,255,0.6);'); ?>"
               onmouseover="if(!<?php echo e($active ? 'true' : 'false'); ?>) this.style.background='rgba(255,255,255,0.06)'; this.style.color='rgba(255,255,255,0.9)';"
               onmouseout="if(!<?php echo e($active ? 'true' : 'false'); ?>) this.style.background=''; this.style.color='rgba(255,255,255,0.6)';">
                <?php if($active): ?>
                <span class="absolute left-0 top-0 h-full w-0.5 rounded-r" style="background:#3b82f6;"></span>
                <?php endif; ?>
                <i data-lucide="<?php echo e($item['icon']); ?>" class="w-4 h-4 flex-shrink-0"></i>
                <span><?php echo e($item['label']); ?></span>
            </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <!-- MASTER DATA section -->
        <?php if(in_array(auth()->user()->role, ['ADMIN', 'STOCK_MANAGER'])): ?>
        <p class="px-2 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,0.3);">Master Data</p>
        <?php $__currentLoopData = $masterDataItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $active = request()->routeIs($item['match']);
            ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium transition-colors duration-150"
               style="<?php echo e($active ? 'background:rgba(37,99,235,0.25); color:#60a5fa;' : 'color:rgba(255,255,255,0.6);'); ?>"
               onmouseover="if(!<?php echo e($active ? 'true' : 'false'); ?>) this.style.background='rgba(255,255,255,0.06)'; this.style.color='rgba(255,255,255,0.9)';"
               onmouseout="if(!<?php echo e($active ? 'true' : 'false'); ?>) this.style.background=''; this.style.color='rgba(255,255,255,0.6)';">
                <i data-lucide="<?php echo e($item['icon']); ?>" class="w-4 h-4 flex-shrink-0"></i>
                <span><?php echo e($item['label']); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

        <!-- MANAGEMENT section -->
        <?php if(auth()->user()->isAdmin()): ?>
        <p class="px-2 pt-4 pb-1 text-xs font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,0.3);">Management</p>
        <?php $__currentLoopData = $mgmtItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $active = request()->routeIs($item['match']);
            ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="flex items-center gap-3 px-3 py-2 rounded text-sm font-medium transition-colors duration-150"
               style="<?php echo e($active ? 'background:rgba(37,99,235,0.25); color:#60a5fa;' : 'color:rgba(255,255,255,0.6);'); ?>"
               onmouseover="if(!<?php echo e($active ? 'true' : 'false'); ?>) this.style.background='rgba(255,255,255,0.06)'; this.style.color='rgba(255,255,255,0.9)';"
               onmouseout="if(!<?php echo e($active ? 'true' : 'false'); ?>) this.style.background=''; this.style.color='rgba(255,255,255,0.6)';">
                <i data-lucide="<?php echo e($item['icon']); ?>" class="w-4 h-4 flex-shrink-0"></i>
                <span><?php echo e($item['label']); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </nav>

    <!-- User Info at bottom -->
    <div class="px-3 py-3 border-t" style="border-color:rgba(255,255,255,0.08);">
        <div class="flex items-center gap-3 px-2 py-2 rounded" style="background:rgba(255,255,255,0.04);">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                 style="background:#2563eb;">
                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-white truncate"><?php echo e(auth()->user()->name); ?></p>
                <p class="text-xs truncate" style="color:rgba(255,255,255,0.4);"><?php echo e(auth()->user()->role_label); ?></p>
            </div>
        </div>
    </div>
</aside>
<?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/components/sidebar.blade.php ENDPATH**/ ?>