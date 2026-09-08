<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type'    => 'button',
    'variant' => 'primary',
    'size'    => 'md',
    'icon'    => null,
    'href'    => null,
]));

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

foreach (array_filter(([
    'type'    => 'button',
    'variant' => 'primary',
    'size'    => 'md',
    'icon'    => null,
    'href'    => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $variants = [
        'primary'   => 'bg-blue-600 hover:bg-blue-700 text-white border border-blue-600',
        'secondary' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white border border-red-600',
        'ghost'     => 'bg-transparent hover:bg-gray-100 text-gray-600 border border-transparent',
        'success'   => 'bg-green-600 hover:bg-green-700 text-white border border-green-600',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-sm',
    ];
    $base    = 'inline-flex items-center gap-2 font-medium rounded transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed';
    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
    $tag     = $href ? 'a' : 'button';
?>

<<?php echo e($tag); ?>

    <?php if($href): ?> href="<?php echo e($href); ?>" <?php else: ?> type="<?php echo e($type); ?>" <?php endif; ?>
    <?php echo e($attributes->merge(['class' => $classes])); ?>>
    <?php if($icon): ?>
        <i data-lucide="<?php echo e($icon); ?>" class="w-4 h-4 flex-shrink-0"></i>
    <?php endif; ?>
    <?php echo e($slot); ?>

</<?php echo e($tag); ?>>
<?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/components/button.blade.php ENDPATH**/ ?>