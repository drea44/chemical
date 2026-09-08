<?php
    $title      = 'Reports';
    $breadcrumb = [['label' => 'Reports', 'url' => route('reports.index')]];
    $tab        = request('tab', 'inventory');
?>

<?php $__env->startSection('title', 'Reports'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Reports</h1>
        <p class="text-sm text-gray-500 mt-0.5">Inventory analysis and compliance reporting</p>
    </div>
    <div class="flex gap-2">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('reports.export-csv', ['type' => $tab])).'','variant' => 'secondary','icon' => 'download','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('reports.export-csv', ['type' => $tab])).'','variant' => 'secondary','icon' => 'download','size' => 'sm']); ?>Export CSV <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
    </div>
</div>

<!-- Summary Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Total Chemicals','value' => ''.e($stats['total_chemicals']).'','icon' => 'flask-conical','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Chemicals','value' => ''.e($stats['total_chemicals']).'','icon' => 'flask-conical','color' => 'blue']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Safe','value' => ''.e($stats['active']).'','icon' => 'check-circle','color' => 'green']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Safe','value' => ''.e($stats['active']).'','icon' => 'check-circle','color' => 'green']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Low Stock','value' => ''.e($stats['low_stock']).'','icon' => 'trending-down','color' => 'yellow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Low Stock','value' => ''.e($stats['low_stock']).'','icon' => 'trending-down','color' => 'yellow']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Needs Attention','value' => ''.e($stats['critical']).'','icon' => 'alert-triangle','color' => 'red']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Needs Attention','value' => ''.e($stats['critical']).'','icon' => 'alert-triangle','color' => 'red']); ?>
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

<!-- Tab Nav -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="border-b border-gray-200 bg-gray-50">
        <nav class="flex gap-0 px-5 pt-3">
            <?php $__currentLoopData = [
                ['tab' => 'inventory',    'label' => 'Inventory Summary',   'icon' => 'package'],
                ['tab' => 'movement',     'label' => 'Stock Movement',       'icon' => 'arrow-left-right'],
                ['tab' => 'expiry',       'label' => 'Expiry Report',        'icon' => 'calendar-x'],
                ['tab' => 'adjustments',  'label' => 'Adjustments',          'icon' => 'sliders-horizontal'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('reports.index', ['tab' => $t['tab']])); ?>"
               class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-t border-b-2 transition-colors mr-1
                      <?php echo e($tab === $t['tab'] ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-white'); ?>">
                <i data-lucide="<?php echo e($t['icon']); ?>" class="w-4 h-4"></i>
                <?php echo e($t['label']); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </div>

    <div class="p-0">

        
        <?php if($tab === 'inventory'): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Min</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiry</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__currentLoopData = $inventorySummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 text-xs"><?php echo e($c->chemical_name); ?></p>
                            <p class="text-xs text-gray-400 font-mono"><?php echo e($c->chemical_code); ?></p>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500"><?php echo e($c->category?->name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-xs text-gray-500"><?php echo e($c->location?->name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-right font-mono text-sm font-semibold text-gray-800">
                            <?php echo e(number_format($c->current_stock, 2)); ?> <?php echo e($c->unit); ?>

                        </td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-400"><?php echo e(number_format($c->minimum_stock, 2)); ?></td>
                        <td class="px-3 py-3 text-xs <?php echo e($c->isExpired() ? 'text-red-600 font-medium' : 'text-gray-500'); ?>">
                            <?php echo e($c->expiry_date?->format('d M Y') ?? '—'); ?>

                        </td>
                        <td class="px-3 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $c->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($c->status)]); ?>
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
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        
        <?php elseif($tab === 'movement'): ?>
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <form method="GET" class="flex gap-3">
                <input type="hidden" name="tab" value="movement">
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700">Filter</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Qty</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Before</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">After</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">By</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $stockMovement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600"><?php echo e($tx->transaction_code); ?></td>
                        <td class="px-3 py-3 text-xs font-medium text-gray-800"><?php echo e($tx->chemical?->chemical_name ?? '—'); ?></td>
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
                        <td class="px-3 py-3 text-right font-mono text-xs font-bold <?php echo e($tx->transaction_type === 'STOCK_IN' ? 'text-green-600' : 'text-red-500'); ?>">
                            <?php echo e($tx->transaction_type === 'STOCK_IN' ? '+' : '-'); ?><?php echo e(number_format($tx->quantity, 2)); ?>

                        </td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-400"><?php echo e(number_format($tx->stock_before, 2)); ?></td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-semibold text-gray-800"><?php echo e(number_format($tx->stock_after, 2)); ?></td>
                        <td class="px-3 py-3 text-xs text-gray-500"><?php echo e($tx->performer?->name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-xs text-gray-400 whitespace-nowrap"><?php echo e($tx->transaction_date?->format('d M Y H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No movement data for selected period</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php elseif($tab === 'expiry'): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expiry Date</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Days Left</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $expiryReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 <?php echo e($c->isExpired() ? 'bg-red-50' : ''); ?>">
                        <td class="px-5 py-3">
                            <a href="<?php echo e(route('chemicals.show', $c)); ?>" class="font-medium text-gray-800 hover:text-blue-600 text-xs"><?php echo e($c->chemical_name); ?></a>
                            <p class="text-xs text-gray-400 font-mono"><?php echo e($c->chemical_code); ?></p>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500"><?php echo e($c->location?->name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-700"><?php echo e(number_format($c->current_stock, 2)); ?> <?php echo e($c->unit); ?></td>
                        <td class="px-3 py-3 text-xs <?php echo e($c->isExpired() ? 'text-red-600 font-semibold' : 'text-gray-700'); ?>">
                            <?php echo e($c->expiry_date->format('d M Y')); ?>

                        </td>
                        <td class="px-3 py-3 text-xs">
                            <?php $days = now()->diffInDays($c->expiry_date, false); ?>
                            <span class="<?php echo e($days < 0 ? 'text-red-600 font-semibold' : ($days <= 30 ? 'text-orange-600 font-medium' : 'text-gray-500')); ?>">
                                <?php echo e($days < 0 ? abs($days) . ' days ago' : $days . ' days'); ?>

                            </span>
                        </td>
                        <td class="px-3 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $c->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($c->status)]); ?>
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
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No chemicals with expiry dates</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php elseif($tab === 'adjustments'): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Code</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemical</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Previous</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Adjusted</th>
                        <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Difference</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Reason</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">By</th>
                        <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $adjustmentReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-xs text-gray-600"><?php echo e($adj->adjustment_code); ?></td>
                        <td class="px-3 py-3 text-xs font-medium text-gray-800"><?php echo e($adj->chemical?->chemical_name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-right font-mono text-xs text-gray-500"><?php echo e(number_format($adj->previous_stock, 2)); ?></td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-semibold text-gray-800"><?php echo e(number_format($adj->adjusted_stock, 2)); ?></td>
                        <td class="px-3 py-3 text-right font-mono text-xs font-bold <?php echo e($adj->difference > 0 ? 'text-green-600' : 'text-red-500'); ?>">
                            <?php echo e($adj->difference > 0 ? '+' : ''); ?><?php echo e(number_format($adj->difference, 2)); ?>

                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500 max-w-xs truncate"><?php echo e($adj->reason); ?></td>
                        <td class="px-3 py-3 text-xs text-gray-500"><?php echo e($adj->adjuster?->name ?? '—'); ?></td>
                        <td class="px-3 py-3 text-xs text-gray-400 whitespace-nowrap"><?php echo e($adj->created_at?->format('d M Y H:i')); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">No adjustments recorded</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/reports/index.blade.php ENDPATH**/ ?>