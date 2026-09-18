@php
    $navItems = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'icon'  => 'layout-dashboard',
            'match' => 'dashboard',
        ],
        [
            'label' => 'Chemical Inventory',
            'route' => 'chemicals.index',
            'icon'  => 'flask-conical',
            'match' => 'chemicals*',
        ],
        [
            'label' => 'Categories',
            'route' => 'categories.index',
            'icon'  => 'tag',
            'match' => 'categories*',
        ],
        [
            'label' => 'Suppliers',
            'route' => 'suppliers.index',
            'icon'  => 'truck',
            'match' => 'suppliers*',
        ],
        [
            'label' => 'Storage Locations',
            'route' => 'locations.index',
            'icon'  => 'map-pin',
            'match' => 'locations*',
        ],
        [
            'label' => 'Stock Adjustment',
            'route' => 'stock.stock-in-out',
            'icon'  => 'arrow-left-right',
            'match' => 'stock.stock-in-out*',
        ],
        [
            'label' => 'QR Code Scanner',
            'route' => 'qr-scanner.index',
            'icon'  => 'scan-qr-code',
            'match' => 'qr-scanner*',
        ],
        [
            'label'    => 'Log Chemical',
            'route'    => 'transactions.master-report',
            'icon'     => 'scroll-text',
            'match'    => 'transactions*',
            'children' => [
                [
                    'label' => 'Master Report',
                    'route' => 'transactions.master-report',
                    'match' => 'transactions.master-report*',
                ],
                [
                    'label' => 'Warning Stock',
                    'route' => 'transactions.warning-stock',
                    'match' => 'transactions.warning-stock*',
                ],
                [
                    'label' => 'Daily Usage Sheet',
                    'route' => 'transactions.index',
                    'match' => 'transactions.index*',
                ],
            ],
        ],
        [
            'label' => 'Stock Change History',
            'route' => 'stock.index',
            'icon'  => 'history',
            'match' => 'stock.index',
        ],
        [
            'label' => 'Reports',
            'route' => 'reports.index',
            'icon'  => 'bar-chart-2',
            'match' => 'reports*',
        ],
        [
            'label' => 'Audit Trail',
            'route' => 'audit-trail.index',
            'icon'  => 'shield-check',
            'match' => 'audit-trail*',
            'roles' => ['ADMIN', 'AUDITOR'],
        ],
        [
            'label' => 'Users & Roles',
            'route' => 'users.index',
            'icon'  => 'users',
            'match' => 'users*',
            'roles' => ['ADMIN'],
        ],
        [
            'label' => 'Settings',
            'route' => 'settings.index',
            'icon'  => 'settings',
            'match' => 'settings*',
            'roles' => ['ADMIN'],
        ],
    ];
@endphp

<aside
    class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 text-white transition-transform duration-200 ease-in-out shadow-xl lg:shadow-none"
    style="background:#131b2e;"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>

    <div class="flex items-center justify-between px-5 py-4 border-b" style="border-color:rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-8 h-8 rounded flex items-center justify-center" style="background:#2563eb;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
                </svg>
            </div>
            <div>
                <p class="text-white text-sm font-semibold leading-tight tracking-wide">Chemical Stock OS</p>
                <p class="text-xs leading-tight" style="color:rgba(255,255,255,0.35);">v1.0.0 Enterprise</p>
            </div>
        </div>

        <button @click="sidebarOpen = false" class="lg:hidden p-1.5 rounded text-gray-400 hover:text-white hover:bg-slate-800 transition-colors" aria-label="Close menu">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
        @foreach($navItems as $item)
            @php
                $allowed = !isset($item['roles']) || in_array(auth()->user()->role, $item['roles']);
                $active  = request()->routeIs($item['match']);
            @endphp
            @if($allowed)
                @if(isset($item['children']))
                    <div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-0.5">
                        <button type="button"
                                @click="open = !open"
                                class="w-full relative flex items-center justify-between px-3 py-2 rounded text-sm font-medium transition-all duration-150"
                                style="{{ $active
                                    ? 'background:rgba(37,99,235,0.18); color:#ffffff; font-weight:600;'
                                    : 'color:rgba(255,255,255,0.55);' }}"
                                onmouseover="if(!{{ $active ? 'true' : 'false' }}) { this.style.background='rgba(255,255,255,0.06)'; this.style.color='rgba(255,255,255,0.9)'; }"
                                onmouseout="if(!{{ $active ? 'true' : 'false' }}) { this.style.background=''; this.style.color='rgba(255,255,255,0.55)'; }">
                            <div class="flex items-center gap-3">
                                @if($active)
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-0.5 rounded-r" style="background:#3b82f6;"></span>
                                @endif
                                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 flex-shrink-0"></i>
                                <span>{{ $item['label'] }}</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-150"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" class="pl-7 pr-1 space-y-0.5 pt-0.5 pb-1">
                            @foreach($item['children'] as $child)
                                @php
                                    $childActive = request()->routeIs($child['match']) || request()->routeIs($child['route']);
                                @endphp
                                <a href="{{ route($child['route']) }}"
                                   class="block px-3 py-1.5 rounded text-xs font-medium transition-all duration-150"
                                   style="{{ $childActive
                                       ? 'background:rgba(37,99,235,0.25); color:#60a5fa; font-weight:600;'
                                       : 'color:rgba(255,255,255,0.45);' }}"
                                   onmouseover="if(!{{ $childActive ? 'true' : 'false' }}) { this.style.background='rgba(255,255,255,0.06)'; this.style.color='rgba(255,255,255,0.85)'; }"
                                   onmouseout="if(!{{ $childActive ? 'true' : 'false' }}) { this.style.background=''; this.style.color='rgba(255,255,255,0.45)'; }">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                <a href="{{ route($item['route']) }}"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-sm font-medium transition-all duration-150"
                   style="{{ $active
                       ? 'background:rgba(37,99,235,0.18); color:#ffffff; font-weight:600;'
                       : 'color:rgba(255,255,255,0.55);' }}"
                   onmouseover="if(!{{ $active ? 'true' : 'false' }}) { this.style.background='rgba(255,255,255,0.06)'; this.style.color='rgba(255,255,255,0.9)'; }"
                   onmouseout="if(!{{ $active ? 'true' : 'false' }}) { this.style.background=''; this.style.color='rgba(255,255,255,0.55)'; }">
                    @if($active)
                    <span class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-0.5 rounded-r" style="background:#3b82f6;"></span>
                    @endif
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
                @endif
            @endif
        @endforeach
    </nav>

    <div class="px-3 py-3 border-t" style="border-color:rgba(255,255,255,0.07);">
        <div class="flex items-center gap-3 px-2 py-2 rounded" style="background:rgba(255,255,255,0.04);">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                 style="background:#2563eb;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate" style="color:rgba(255,255,255,0.4);">{{ auth()->user()->role_label }}</p>
            </div>
        </div>
    </div>
</aside>

