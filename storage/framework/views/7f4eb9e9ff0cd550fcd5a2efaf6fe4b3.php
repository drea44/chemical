<?php
    $title      = 'Suppliers';
    $breadcrumb = [['label' => 'Suppliers', 'url' => route('suppliers.index')]];
?>

<?php $__env->startSection('title', 'Suppliers'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Chemical Suppliers</h1>
        <p class="text-sm text-gray-500 mt-0.5"><?php echo e($suppliers->total()); ?> suppliers registered</p>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Chemical::class)): ?>
    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('suppliers.create')).'','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('suppliers.create')).'','icon' => 'plus']); ?>Add Supplier <?php echo $__env->renderComponent(); ?>
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

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search supplier name, contact, email, phone..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Statuses</option>
            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
            <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        <?php if(request()->hasAny(['search', 'status'])): ?>
        <a href="<?php echo e(route('suppliers.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Supplier Name</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact Person</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email / Phone</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemicals</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-gray-900"><?php echo e($supplier->name); ?></p>
                        <?php if($supplier->website): ?>
                        <a href="<?php echo e($supplier->website); ?>" target="_blank" class="text-xs text-blue-600 hover:underline flex items-center gap-1 mt-0.5">
                            <?php echo e(parse_url($supplier->website, PHP_URL_HOST) ?? $supplier->website); ?>

                            <i data-lucide="external-link" class="w-3 h-3"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-700 font-medium"><?php echo e($supplier->contact_person ?: '—'); ?></td>
                    <td class="px-4 py-3.5 text-xs text-gray-500">
                        <?php if($supplier->email): ?>
                        <p class="flex items-center gap-1"><i data-lucide="mail" class="w-3 h-3 text-gray-400"></i><?php echo e($supplier->email); ?></p>
                        <?php endif; ?>
                        <?php if($supplier->phone): ?>
                        <p class="flex items-center gap-1 mt-0.5"><i data-lucide="phone" class="w-3 h-3 text-gray-400"></i><?php echo e($supplier->phone); ?></p>
                        <?php endif; ?>
                        <?php if(!$supplier->email && !$supplier->phone): ?>
                        <span>—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            <?php echo e($supplier->chemicals_count); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($supplier->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                            <?php echo e(ucfirst($supplier->status)); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Chemical::class)): ?>
                            <a href="<?php echo e(route('suppliers.edit', $supplier)); ?>" class="p-1 text-gray-400 hover:text-blue-600 rounded transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <?php if($supplier->chemicals_count === 0): ?>
                            <form action="<?php echo e(route('suppliers.destroy', $supplier)); ?>" method="POST" onsubmit="return confirm('Delete supplier <?php echo e($supplier->name); ?>?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600 rounded transition-colors" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No suppliers found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($suppliers->hasPages()): ?>
    <div class="p-4 border-t border-gray-100">
        <?php echo e($suppliers->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/suppliers/index.blade.php ENDPATH**/ ?>