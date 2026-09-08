@extends('layouts.app')

@php
    $title      = 'Create User';
    $breadcrumb = [
        ['label' => 'Users', 'url' => route('users.index')],
        ['label' => 'Create User', 'url' => '#'],
    ];
@endphp

@section('title', 'Create User')

@section('content')

<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold text-gray-900">Create New User</h1>
        <x-button href="{{ route('users.index') }}" variant="secondary" icon="arrow-left" size="sm">Back</x-button>
    </div>

    <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
        @csrf
        <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            <x-form-input label="Full Name" name="name" :required="true" placeholder="e.g. John Doe" />
            <x-form-input label="Email Address" name="email" type="email" :required="true" placeholder="john@example.com" />
            <x-form-input label="Password" name="password" type="password" :required="true" hint="Minimum 8 characters with mixed case and numbers" />
            <x-form-select label="Role" name="role" :required="true" placeholder="Select role"
                :options="['ADMIN' => 'Administrator', 'STOCK_MANAGER' => 'Stock Manager', 'AUDITOR' => 'Auditor', 'VIEWER' => 'Viewer']" />
            <div class="grid grid-cols-2 gap-4">
                <x-form-input label="Department" name="department" placeholder="e.g. Quality Control" />
                <x-form-input label="Position" name="position" placeholder="e.g. Lab Manager" />
            </div>
            <x-form-select label="Status" name="status" :selected="'active'"
                :options="['active' => 'Active', 'inactive' => 'Inactive']" />
        </div>
        <div class="flex justify-end gap-3">
            <x-button href="{{ route('users.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" icon="user-plus">Create User</x-button>
        </div>
    </form>
</div>

@endsection
