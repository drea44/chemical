@props(['title' => 'Dashboard', 'breadcrumb' => []])

@php
    use App\Services\ChemicalService;
    $notifications = app(ChemicalService::class)->getNotificationCounts();
@endphp

<header class="sticky top-0 z-30 bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center justify-between min-w-0">
    <!-- Left: Toggle + Breadcrumb -->
    <div class="flex items-center gap-3 min-w-0">
        <button @click="sidebarOpen = !sidebarOpen"
                class="p-1.5 rounded hover:bg-gray-100 text-gray-500 transition-colors flex-shrink-0"
                aria-label="Toggle navigation">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <!-- Breadcrumb on desktop / tablet -->
        <nav class="hidden md:flex items-center gap-1 text-sm truncate">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
            </a>
            @foreach($breadcrumb as $crumb)
            <span class="text-gray-300 mx-1">/</span>
            @if($loop->last)
                <span class="text-gray-700 font-medium truncate max-w-xs">{{ $crumb['label'] }}</span>
            @else
                <a href="{{ $crumb['url'] }}" class="text-gray-400 hover:text-gray-600 truncate max-w-xs">{{ $crumb['label'] }}</a>
            @endif
            @endforeach
            @if(empty($breadcrumb))
                <span class="text-gray-300 mx-1">/</span>
                <span class="text-gray-700 font-medium truncate">{{ $title }}</span>
            @endif
        </nav>

        <!-- Title fallback on mobile -->
        <span class="md:hidden text-sm font-semibold text-gray-800 truncate max-w-[150px] sm:max-w-[260px]">{{ $title }}</span>
    </div>

    <!-- Right: Notifications + User -->
    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">

        <!-- Notification Bell -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="relative p-2 rounded hover:bg-gray-100 text-gray-500 transition-colors"
                    aria-label="Notifications">
                <i data-lucide="bell" class="w-5 h-5"></i>
                @if($notifications['total'] > 0)
                <span class="absolute -top-0.5 -right-0.5 w-4.5 h-4.5 text-xs bg-red-500 text-white rounded-full flex items-center justify-center font-semibold"
                      style="width:18px;height:18px;font-size:10px;">
                    {{ min($notifications['total'], 99) }}
                </span>
                @endif
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">Alerts</p>
                </div>
                <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                    @if($notifications['critical'] > 0)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-800 font-medium">{{ $notifications['critical'] }} Critical Stock</p>
                            <p class="text-xs text-gray-400">Immediate action required</p>
                        </div>
                    </div>
                    @endif
                    @if($notifications['low_stock'] > 0)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-2 h-2 rounded-full bg-yellow-500 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-800 font-medium">{{ $notifications['low_stock'] }} Low Stock</p>
                            <p class="text-xs text-gray-400">Below minimum threshold</p>
                        </div>
                    </div>
                    @endif
                    @if($notifications['expiring_soon'] > 0)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-2 h-2 rounded-full bg-orange-500 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-800 font-medium">{{ $notifications['expiring_soon'] }} Expiring Soon</p>
                            <p class="text-xs text-gray-400">Check expiry dates</p>
                        </div>
                    </div>
                    @endif
                    @if($notifications['expired'] > 0)
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="w-2 h-2 rounded-full bg-red-700 flex-shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-800 font-medium">{{ $notifications['expired'] }} Expired</p>
                            <p class="text-xs text-gray-400">Chemicals past expiry</p>
                        </div>
                    </div>
                    @endif
                    @if($notifications['total'] === 0)
                    <div class="px-4 py-6 text-center">
                        <i data-lucide="check-circle" class="w-8 h-8 text-green-400 mx-auto mb-2"></i>
                        <p class="text-sm text-gray-500">All systems normal</p>
                    </div>
                    @endif
                </div>
                <div class="px-4 py-2 border-t border-gray-100">
                    <a href="{{ route('chemicals.index', ['status' => 'CRITICAL']) }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">View all alerts →</a>
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-2 px-3 py-1.5 rounded hover:bg-gray-100 text-gray-700 transition-colors text-sm">
                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="font-medium hidden sm:block">{{ auth()->user()->name }}</span>
                <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-gray-400"></i>
            </button>

            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700">
                        {{ auth()->user()->role_label }}
                    </span>
                </div>
                <div class="py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
