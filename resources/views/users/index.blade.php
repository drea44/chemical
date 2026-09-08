@extends('layouts.app')

@php
    $title      = 'User Management';
    $breadcrumb = [['label' => 'Users', 'url' => route('users.index')]];
@endphp

@section('title', 'User Management')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">User Management</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $users->total() }} users registered</p>
    </div>
    <x-button href="{{ route('users.create') }}" icon="user-plus">Add User</x-button>
</div>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-lg p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="relative flex-1 min-w-48">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, department..."
                   class="pl-9 w-full rounded border border-gray-300 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
        </div>
        <select name="role" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Roles</option>
            @foreach(['ADMIN' => 'Administrator', 'STOCK_MANAGER' => 'Stock Manager', 'AUDITOR' => 'Auditor', 'VIEWER' => 'Viewer'] as $v => $l)
            <option value="{{ $v }}" @selected(request('role') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none bg-white">
            <option value="">All Statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700 transition-colors">Filter</button>
        @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 text-gray-600 rounded text-sm hover:bg-gray-50">Clear</a>
        @endif
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
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        @php
                            $roleColors = ['ADMIN' => 'bg-purple-50 text-purple-700', 'STOCK_MANAGER' => 'bg-blue-50 text-blue-700', 'AUDITOR' => 'bg-green-50 text-green-700', 'VIEWER' => 'bg-gray-100 text-gray-600'];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $user->role_label }}
                        </span>
                    </td>
                    <td class="px-3 py-3 text-xs text-gray-500">
                        {{ $user->department ?? '—' }}
                        @if($user->position)
                        <p class="text-gray-400">{{ $user->position }}</p>
                        @endif
                    </td>
                    <td class="px-3 py-3"><x-status-badge :status="$user->status" /></td>
                    <td class="px-3 py-3 text-xs text-gray-400">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('users.edit', $user) }}" title="Edit"
                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('users.deactivate', $user) }}">
                                @csrf
                                <button type="submit" title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                        class="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded transition-colors">
                                    <i data-lucide="{{ $user->status === 'active' ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('users.reset-password', $user) }}"
                                  onsubmit="return confirm('Reset password for {{ addslashes($user->name) }}?')">
                                @csrf
                                <button type="submit" title="Reset Password"
                                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded transition-colors">
                                    <i data-lucide="key" class="w-4 h-4"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center">
                        <i data-lucide="users" class="w-10 h-10 text-gray-200 mx-auto mb-3"></i>
                        <p class="text-sm text-gray-400">No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between">
        <p class="text-xs text-gray-500">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</p>
        {{ $users->links('pagination::simple-tailwind') }}
    </div>
    @endif
</div>

@endsection
