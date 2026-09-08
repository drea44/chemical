<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label' => '', 'name' => '', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'hint' => '']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['label' => '', 'name' => '', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'hint' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="space-y-1">
    <?php if($label): ?>
    <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700">
        <?php echo e($label); ?><?php if($required): ?><span class="text-red-500 ml-0.5">*</span><?php endif; ?>
    </label>
    <?php endif; ?>
    <input
        type="<?php echo e($type); ?>"
        id="<?php echo e($name); ?>"
        name="<?php echo e($name); ?>"
        value="<?php echo e(old($name, $value)); ?>"
        placeholder="<?php echo e($placeholder); ?>"
        <?php if($required): ?> required <?php endif; ?>
        <?php echo e($attributes->merge(['class' => 'block w-full rounded border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition-colors ' . ($errors->has($name) ? 'border-red-300 bg-red-50' : '')])); ?>

    >
    <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-xs text-red-600 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <?php if($hint && !$errors->has($name)): ?>
        <p class="text-xs text-gray-400"><?php echo e($hint); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/components/form-input.blade.php ENDPATH**/ ?>