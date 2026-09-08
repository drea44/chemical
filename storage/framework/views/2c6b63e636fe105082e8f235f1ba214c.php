<?php
    $title      = 'User Management';
    $breadcrumb = [['label' => 'Users', 'url' => route('users.index')]];
?>

<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">User Management</h1>
        <p class="text-sm text-gray-500 mt-0.5"><?php echo e($users->total()); ?> users registered</p>
    </div>
    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('users.create')).'','icon' => 'user-plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('users.create')).'','icon' => 'user-plus']); ?>Add User <?php echo $__env->renderComponent(); ?>
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

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search name, email, department..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select name="role" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Roles</option>
            <?php $__currentLoopData = ['ADMIN' => 'Administrator', 'STOCK_MANAGER' => 'Stock Manager', 'AUDITOR' => 'Auditor', 'VIEWER' => 'Viewer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($v); ?>" <?php if(request('role') === $v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Statuses</option>
            <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
            <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        <?php if(request()->hasAny(['search','role','status'])): ?>
        <a href="<?php echo e(route('users.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Department</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Login</th>
                    <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800"><?php echo e($user->name); ?></p>
                                <p class="text-xs text-gray-400"><?php echo e($user->email); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        <?php
                            $roleColors = ['ADMIN' => 'bg-purple-50 text-purple-700', 'STOCK_MANAGER' => 'bg-blue-50 text-blue-700', 'AUDITOR' => 'bg-green-50 text-green-700', 'VIEWER' => 'bg-gray-100 text-gray-600'];
                        ?>
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold <?php echo e($roleColors[$user->role] ?? 'bg-gray-100 text-gray-600'); ?>">
                            <?php echo e($user->role_label); ?>

                        </span>
                    </td>
                    <td class="px-3 py-3 text-xs text-gray-500">
                        <?php echo e($user->department ?? '—'); ?>

                        <?php if($user->position): ?>
                        <p class="text-gray-400"><?php echo e($user->position); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-3 py-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $user->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->status)]); ?>
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
                    <td class="px-3 py-3 text-xs text-gray-400">
                        <?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'); ?>

                    </td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1">
                            <a href="<?php echo e(route('users.edit', $user)); ?>" title="Edit"
                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <?php if(auth()->id() !== $user->id): ?>
                            <form method="POST" action="<?php echo e(route('users.deactivate', $user)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" title="<?php echo e($user->status === 'active' ? 'Deactivate' : 'Activate'); ?>"
                                        class="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded transition-colors">
                                    <i data-lucide="<?php echo e($user->status === 'active' ? 'user-x' : 'user-check'); ?>" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form method="POST" action="<?php echo e(route('users.reset-password', $user)); ?>"
                                  onsubmit="return confirm('Reset password for <?php echo e(addslashes($user->name)); ?>?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" title="Reset Password"
                                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition-colors">
                                    <i data-lucide="key" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <i data-lucide="users" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No users found</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($users->hasPages()): ?>
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">Showing <?php echo e($users->firstItem()); ?>–<?php echo e($users->lastItem()); ?> of <?php echo e($users->total()); ?></p>
        <?php echo e($users->links('pagination::simple-tailwind')); ?>

    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/users/index.blade.php ENDPATH**/ ?>