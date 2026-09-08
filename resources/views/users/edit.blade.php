@extends('layouts.app')

@php
    $title      = 'Edit ' . $user->name;
    $breadcrumb = [
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Edit User', 'url' => '#'],
    ];
@endphp

@section('title', 'Edit User')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900">Edit User</h1>
        <x-button href="{{ route('users.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
        @csrf @method('PUT')
        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <x-form-input label="Full Name" name="name" :value="$user->name" :required="true" />
            <x-form-input label="Email Address" name="email" type="email" :value="$user->email" :required="true" />
            <x-form-select label="Role" name="role" :required="true" :selected="$user->role"
                :options="['ADMIN' => 'Administrator', 'STOCK_MANAGER' => 'Stock Manager', 'AUDITOR' => 'Auditor', 'VIEWER' => 'Viewer']" />
            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Department" name="department" :value="$user->department" placeholder="e.g. Quality Control" />
                <x-form-input label="Position" name="position" :value="$user->position" placeholder="e.g. Lab Manager" />
            </div>
            <x-form-select label="Status" name="status" :selected="$user->status"
                :options="['active' => 'Active', 'inactive' => 'Inactive']" />
        </div>
        <div class="flex justify-end gap-3">
            <x-button href="{{ route('users.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" icon="save">Save Changes</x-button>
        </div>
    </form>
</div>

@endsection
