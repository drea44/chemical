<?php
    $title      = 'Dashboard';
    $breadcrumb = [];
?>

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Overview</h1>
        <p class="text-sm text-gray-500 mt-0.5">Chemical inventory status — <?php echo e(now()->format('d M Y, H:i')); ?></p>
    </div>
    <div class="flex gap-2">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Chemical::class)): ?>
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('chemicals.create')).'','icon' => 'plus','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('chemicals.create')).'','icon' => 'plus','size' => 'sm']); ?>Add Chemical <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4 mb-6">
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Total Chemicals','value' => ''.e($stats['total_chemicals']).'','icon' => 'flask-conical','color' => 'blue','href' => ''.e(route('chemicals.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Chemicals','value' => ''.e($stats['total_chemicals']).'','icon' => 'flask-conical','color' => 'blue','href' => ''.e(route('chemicals.index')).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Total Stock','value' => ''.e(number_format($stats['total_stock'], 0)).'','icon' => 'package','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Stock','value' => ''.e(number_format($stats['total_stock'], 0)).'','icon' => 'package','color' => 'blue']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Low Stock','value' => ''.e($stats['low_stock']).'','icon' => 'trending-down','color' => 'yellow','href' => ''.e(route('chemicals.index', ['status' => 'LOW'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Low Stock','value' => ''.e($stats['low_stock']).'','icon' => 'trending-down','color' => 'yellow','href' => ''.e(route('chemicals.index', ['status' => 'LOW'])).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Expiring Soon','value' => ''.e($stats['expiring_soon']).'','icon' => 'calendar-x','color' => 'orange','href' => ''.e(route('chemicals.index', ['status' => 'EXPIRING_SOON'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Expiring Soon','value' => ''.e($stats['expiring_soon']).'','icon' => 'calendar-x','color' => 'orange','href' => ''.e(route('chemicals.index', ['status' => 'EXPIRING_SOON'])).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Critical','value' => ''.e($stats['critical_stock']).'','icon' => 'alert-triangle','color' => 'red','href' => ''.e(route('chemicals.index', ['status' => 'CRITICAL'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Critical','value' => ''.e($stats['critical_stock']).'','icon' => 'alert-triangle','color' => 'red','href' => ''.e(route('chemicals.index', ['status' => 'CRITICAL'])).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Expired','value' => ''.e($stats['expired']).'','icon' => 'x-circle','color' => 'red','href' => ''.e(route('chemicals.index', ['status' => 'EXPIRED'])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Expired','value' => ''.e($stats['expired']).'','icon' => 'x-circle','color' => 'red','href' => ''.e(route('chemicals.index', ['status' => 'EXPIRED'])).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

    <!-- Stock Movement Bar Chart -->
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Stock Movement</h2>
                <p class="text-xs text-gray-400">Last 7 days · Inbound vs Outbound</p>
            </div>
            <div class="flex gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-blue-500 inline-block"></span>Inbound</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-2 rounded-sm bg-red-400 inline-block"></span>Outbound</span>
            </div>
        </div>
        <canvas id="stockMovementChart" height="200"></canvas>
    </div>

    <!-- Status Doughnut Chart -->
    <div class="bg-white border border-gray-200 rounded-lg p-5">
        <div class="mb-4">
            <h2 class="text-sm font-semibold text-gray-900">Stock Status</h2>
            <p class="text-xs text-gray-400">Current distribution</p>
        </div>
        <canvas id="stockStatusChart" height="180"></canvas>
        <div class="mt-4 space-y-2">
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>Safe</span>
                <span class="font-semibold text-gray-700"><?php echo e($stockStatus['safe']); ?></span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span>Low</span>
                <span class="font-semibold text-gray-700"><?php echo e($stockStatus['low']); ?></span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>Critical</span>
                <span class="font-semibold text-gray-700"><?php echo e($stockStatus['critical']); ?></span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>Expiring Soon</span>
                <span class="font-semibold text-gray-700"><?php echo e($stockStatus['expiring_soon']); ?></span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-red-800 inline-block"></span>Expired</span>
                <span class="font-semibold text-gray-700"><?php echo e($stockStatus['expired']); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Row: Recent Activity + Critical Alerts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Recent Inventory Activity</h2>
            <a href="<?php echo e(route('transactions.index')); ?>" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <a href="<?php echo e(route('chemicals.show', $tx->chemical_id)); ?>" class="font-medium text-gray-800 hover:text-blue-600 text-xs">
                                <?php echo e($tx->chemical?->chemical_name ?? 'N/A'); ?>

                            </a>
                        </td>
                        <td class="px-3 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $tx->transaction_type]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tx->transaction_type)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-600">
                            <?php echo e($tx->transaction_type === 'STOCK_IN' ? '+' : '-'); ?><?php echo e(number_format($tx->quantity, 1)); ?>

                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500"><?php echo e($tx->performer?->name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-xs text-gray-400"><?php echo e($tx->transaction_date?->format('d M H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">No recent activity</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Critical Alerts -->
    <div class="bg-white border border-gray-200 rounded-lg">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Critical Alerts</h2>
            <?php if($criticalAlerts->count() > 0): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-600">
                <?php echo e($criticalAlerts->count()); ?>

            </span>
            <?php endif; ?>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $criticalAlerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <a href="<?php echo e(route('chemicals.show', $chem)); ?>" class="text-xs font-semibold text-gray-800 hover:text-blue-600 truncate block">
                            <?php echo e($chem->chemical_name); ?>

                        </a>
                        <p class="text-xs text-gray-400 mt-0.5"><?php echo e($chem->location?->name ?? 'No location'); ?></p>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $chem->status,'size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($chem->status),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                </div>
                <div class="mt-1.5 flex items-center gap-2 text-xs text-gray-500">
                    <?php if($chem->status === 'EXPIRED'): ?>
                        <i data-lucide="calendar-x" class="w-3 h-3 text-red-500"></i>
                        Expired <?php echo e($chem->expiry_date?->format('d M Y')); ?>

                    <?php elseif($chem->status === 'EXPIRING_SOON'): ?>
                        <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
                        Expires <?php echo e($chem->expiry_date?->diffForHumans()); ?>

                    <?php else: ?>
                        <i data-lucide="trending-down" class="w-3 h-3 text-red-500"></i>
                        Stock: <?php echo e(number_format($chem->current_stock, 1)); ?> <?php echo e($chem->unit); ?>

                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-5 py-8 text-center">
                <i data-lucide="check-circle" class="w-8 h-8 text-green-400 mx-auto mb-2"></i>
                <p class="text-sm text-gray-400">All chemicals are within safe limits</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const movementData = <?php echo json_encode($stockMovement, 15, 512) ?>;
const statusData   = <?php echo json_encode($stockStatus, 15, 512) ?>;

// Bar Chart — Stock Movement
new Chart(document.getElementById('stockMovementChart'), {
    type: 'bar',
    data: {
        labels: movementData.days,
        datasets: [
            {
                label: 'Inbound',
                data: movementData.inbound,
                backgroundColor: 'rgba(37,99,235,0.75)',
                borderRadius: 4,
                borderSkipped: false,
            },
            {
                label: 'Outbound',
                data: movementData.outbound,
                backgroundColor: 'rgba(239,68,68,0.65)',
                borderRadius: 4,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, precision: 0 }, beginAtZero: true }
        }
    }
});

// Doughnut Chart — Stock Status
new Chart(document.getElementById('stockStatusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Safe', 'Low', 'Critical', 'Expiring Soon', 'Expired'],
        datasets: [{
            data: [
                statusData.safe,
                statusData.low,
                statusData.critical,
                statusData.expiring_soon,
                statusData.expired,
            ],
            backgroundColor: ['#22c55e','#eab308','#ef4444','#f97316','#991b1b'],
            borderWidth: 2,
            borderColor: '#ffffff',
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: { legend: { display: false } },
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/dashboard/index.blade.php ENDPATH**/ ?>