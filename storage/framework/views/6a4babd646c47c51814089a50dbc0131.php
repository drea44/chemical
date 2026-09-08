<?php
    $title      = 'Chemical Categories';
    $breadcrumb = [['label' => 'Categories', 'url' => route('categories.index')]];
?>

<?php $__env->startSection('title', 'Chemical Categories'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Chemical Categories</h1>
        <p class="text-sm text-gray-500 mt-0.5"><?php echo e($categories->total()); ?> categories defined</p>
    </div>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Chemical::class)): ?>
    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('categories.create')).'','icon' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('categories.create')).'','icon' => 'plus']); ?>Add Category <?php echo $__env->renderComponent(); ?>
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
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search category name or description..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        <?php if(request()->has('search')): ?>
        <a href="<?php echo e(route('categories.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Category Name</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Color Tag</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Chemicals</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3.5 font-semibold text-gray-900">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: <?php echo e($cat->color ?: '#3b82f6'); ?>"></span>
                            <span><?php echo e($cat->name); ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium border"
                              style="background-color: <?php echo e($cat->color ? $cat->color . '15' : '#eff6ff'); ?>; color: <?php echo e($cat->color ?: '#1d4ed8'); ?>; border-color: <?php echo e($cat->color ? $cat->color . '40' : '#bfdbfe'); ?>">
                            <?php echo e($cat->color ?: '#3b82f6'); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3.5 text-xs text-gray-500 max-w-xs truncate"><?php echo e($cat->description ?: '—'); ?></td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            <?php echo e($cat->chemicals_count); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($cat->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'); ?>">
                            <?php echo e(ucfirst($cat->status)); ?>

                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', \App\Models\Chemical::class)): ?>
                            <a href="<?php echo e(route('categories.edit', $cat)); ?>" class="p-1 text-gray-400 hover:text-blue-600 rounded transition-colors" title="Edit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <?php if($cat->chemicals_count === 0): ?>
                            <form action="<?php echo e(route('categories.destroy', $cat)); ?>" method="POST" onsubmit="return confirm('Delete category <?php echo e($cat->name); ?>?')">
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
                    <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No categories found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($categories->hasPages()): ?>
    <div class="p-4 border-t border-gray-100">
        <?php echo e($categories->links()); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/categories/index.blade.php ENDPATH**/ ?>