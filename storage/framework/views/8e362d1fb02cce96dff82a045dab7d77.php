<?php
    $title      = 'Audit Trail';
    $breadcrumb = [['label' => 'Audit Trail', 'url' => route('audit-trail.index')]];
?>

<?php $__env->startSection('title', 'Audit Trail'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Audit Trail</h1>
        <p class="text-sm text-gray-500 mt-0.5"><?php echo e($logs->total()); ?> system events recorded</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search action, module, IP..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select name="user" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Users</option>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>" <?php if(request('user') == $u->id): echo 'selected'; endif; ?>><?php echo e($u->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="action" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Actions</option>
            <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($a); ?>" <?php if(request('action') === $a): echo 'selected'; endif; ?>><?php echo e(ucfirst($a)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="module" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Modules</option>
            <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($m); ?>" <?php if(request('module') === $m): echo 'selected'; endif; ?>><?php echo e($m); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
               class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
               class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        <?php if(request()->hasAny(['search','user','action','module','date_from','date_to'])): ?>
        <a href="<?php echo e(route('audit-trail.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Timestamp</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Module</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Changes</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">IP Address</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $actionColors = [
                        'login'    => 'bg-blue-50 text-blue-700',
                        'logout'   => 'bg-gray-100 text-gray-600',
                        'created'  => 'bg-green-50 text-green-700',
                        'updated'  => 'bg-yellow-50 text-yellow-700',
                        'deleted'  => 'bg-red-50 text-red-700',
                        'stock_in' => 'bg-green-50 text-green-700',
                        'stock_out'=> 'bg-red-50 text-red-700',
                        'adjusted' => 'bg-orange-50 text-orange-700',
                        'exported' => 'bg-purple-50 text-purple-700',
                    ];
                    $ac = $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600';
                ?>
                <tr class="hover:bg-gray-50 transition-colors <?php echo e($log->status === 'failed' ? 'bg-red-50' : ''); ?>">
                    <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                        <p class="font-medium text-gray-700"><?php echo e($log->created_at->format('d M Y')); ?></p>
                        <p class="text-gray-400"><?php echo e($log->created_at->format('H:i:s')); ?></p>
                    </td>
                    <td class="px-3 py-3">
                        <?php if($log->user): ?>
                        <p class="text-xs font-medium text-gray-800"><?php echo e($log->user->name); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($log->user->role_label); ?></p>
                        <?php else: ?>
                        <p class="text-xs text-gray-400 italic">Unknown / Guest</p>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold <?php echo e($ac); ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $log->action))); ?>

                        </span>
                    </td>
                    <td class="px-3 py-3 text-xs text-gray-600">
                        <p class="font-medium"><?php echo e($log->module); ?></p>
                        <?php if($log->record_type && $log->record_id): ?>
                        <p class="text-gray-400">#<?php echo e($log->record_id); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3 text-xs text-gray-500 max-w-xs" x-data="{ open: false }">
                        <?php if($log->old_values || $log->new_values): ?>
                        <button @click="open = !open" class="text-blue-600 hover:text-blue-800 text-xs underline">
                            View changes
                        </button>
                        <div x-show="open" class="mt-2 p-2 bg-gray-50 rounded border border-gray-200 text-xs font-mono whitespace-pre-wrap max-h-32 overflow-y-auto">
                            <?php if($log->old_values): ?>
                            <p class="text-red-500 font-semibold mb-1">Before:</p>
                            <p class="text-red-400"><?php echo e(json_encode($log->old_values, JSON_PRETTY_PRINT)); ?></p>
                            <?php endif; ?>
                            <?php if($log->new_values): ?>
                            <p class="text-green-600 font-semibold mt-1 mb-1">After:</p>
                            <p class="text-green-500"><?php echo e(json_encode($log->new_values, JSON_PRETTY_PRINT)); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-gray-300">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3 text-xs font-mono text-gray-500"><?php echo e($log->ip_address ?? '—'); ?></td>
                    <td class="px-3 py-3">
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $log->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($log->status)]); ?>
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
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <i data-lucide="shield-check" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No audit logs found</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($logs->hasPages()): ?>
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">Showing <?php echo e($logs->firstItem()); ?>–<?php echo e($logs->lastItem()); ?> of <?php echo e($logs->total()); ?></p>
        <?php echo e($logs->links('pagination::simple-tailwind')); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/audit-trail/index.blade.php ENDPATH**/ ?>