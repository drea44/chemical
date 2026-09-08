<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label'   => '',
    'value'   => '',
    'icon'    => 'box',
    'color'   => 'blue',
    'trend'   => null,
    'trendUp' => null,
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
    'label'   => '',
    'value'   => '',
    'icon'    => 'box',
    'color'   => 'blue',
    'trend'   => null,
    'trendUp' => null,
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
    $colors = [
        'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-600',  'border' => 'border-blue-100'],
        'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-600', 'border' => 'border-green-100'],
        'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-600','border' => 'border-yellow-100'],
        'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-600',   'border' => 'border-red-100'],
        'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-600','border' => 'border-orange-100'],
        'gray'   => ['bg' => 'bg-gray-50',   'icon' => 'text-gray-600',  'border' => 'border-gray-100'],
    ];
    $c = $colors[$color] ?? $colors['blue'];
    $tag = $href ? 'a' : 'div';
?>

<<?php echo e($tag); ?> <?php if($href): ?> href="<?php echo e($href); ?>" <?php endif; ?>
    class="bg-white border border-gray-200 rounded-lg p-5 flex items-start gap-4 <?php echo e($href ? 'hover:shadow-md transition-shadow cursor-pointer' : ''); ?>">
    <div class="w-10 h-10 rounded-lg <?php echo e($c['bg']); ?> <?php echo e($c['border']); ?> border flex items-center justify-center flex-shrink-0">
        <i data-lucide="<?php echo e($icon); ?>" class="w-5 h-5 <?php echo e($c['icon']); ?>"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide"><?php echo e($label); ?></p>
        <p class="text-2xl font-bold text-gray-900 mt-0.5 leading-none"><?php echo e($value); ?></p>
        <?php if($trend): ?>
        <p class="text-xs mt-1 <?php echo e($trendUp ? 'text-green-600' : 'text-red-500'); ?>">
            <i data-lucide="<?php echo e($trendUp ? 'trending-up' : 'trending-down'); ?>" class="w-3 h-3 inline"></i>
            <?php echo e($trend); ?>

        </p>
        <?php endif; ?>
    </div>
</<?php echo e($tag); ?>>
<?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/components/stat-card.blade.php ENDPATH**/ ?>