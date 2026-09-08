<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status' => 'SAFE', 'size' => 'sm']));

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

foreach (array_filter((['status' => 'SAFE', 'size' => 'sm']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $map = [
        'SAFE'          => ['bg' => 'bg-green-50',  'text' => 'text-green-700',  'dot' => 'bg-green-500',  'label' => 'Safe'],
        'LOW'           => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500', 'label' => 'Low'],
        'CRITICAL'      => ['bg' => 'bg-red-50',    'text' => 'text-red-700',    'dot' => 'bg-red-500',    'label' => 'Critical'],
        'EXPIRED'       => ['bg' => 'bg-red-100',   'text' => 'text-red-800',    'dot' => 'bg-red-700',    'label' => 'Expired'],
        'EXPIRING_SOON' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500', 'label' => 'Expiring Soon'],
        'active'        => ['bg' => 'bg-green-50',  'text' => 'text-green-700',  'dot' => 'bg-green-500',  'label' => 'Active'],
        'inactive'      => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600',   'dot' => 'bg-gray-400',   'label' => 'Inactive'],
        'completed'     => ['bg' => 'bg-green-50',  'text' => 'text-green-700',  'dot' => 'bg-green-500',  'label' => 'Completed'],
        'pending'       => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500', 'label' => 'Pending'],
        'rejected'      => ['bg' => 'bg-red-50',    'text' => 'text-red-700',    'dot' => 'bg-red-500',    'label' => 'Rejected'],
        'approved'      => ['bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500',   'label' => 'Approved'],
        'STOCK_IN'      => ['bg' => 'bg-green-50',  'text' => 'text-green-700',  'dot' => 'bg-green-500',  'label' => 'Stock In'],
        'STOCK_OUT'     => ['bg' => 'bg-red-50',    'text' => 'text-red-700',    'dot' => 'bg-red-500',    'label' => 'Stock Out'],
        'ADJUSTMENT'    => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500', 'label' => 'Adjustment'],
        'TRANSFER'      => ['bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500',   'label' => 'Transfer'],
    ];
    $s = $map[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => $status];
    $px = $size === 'sm' ? 'px-2 py-0.5' : 'px-3 py-1';
    $text = $size === 'sm' ? 'text-xs' : 'text-sm';
?>

<span class="inline-flex items-center gap-1.5 <?php echo e($px); ?> rounded <?php echo e($s['bg']); ?> <?php echo e($s['text']); ?> <?php echo e($text); ?> font-medium whitespace-nowrap">
    <span class="w-1.5 h-1.5 rounded-full <?php echo e($s['dot']); ?> flex-shrink-0"></span>
    <?php echo e($s['label']); ?>

</span>
<?php /**PATH C:\xampp\htdocs\chemical 2\resources\views/components/status-badge.blade.php ENDPATH**/ ?>