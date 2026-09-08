@props(['status' => 'SAFE', 'size' => 'sm'])

@php
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
@endphp

<span class="inline-flex items-center gap-1.5 {{ $px }} rounded {{ $s['bg'] }} {{ $s['text'] }} {{ $text }} font-medium whitespace-nowrap">
    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }} flex-shrink-0"></span>
    {{ $s['label'] }}
</span>
