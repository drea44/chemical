<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        AuditLogService::logCreated('User', $user->id, ['name' => $user->name, 'role' => $user->role]);
        return redirect()->route('users.index')->with('success', "User '{$user->name}' created successfully.");
    }

    public function show(User $user)
    {
        $this->authorize('update', $user);
        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $old = $user->only(['name', 'role', 'status', 'department']);
        $user->update($request->validated());
        AuditLogService::logUpdated('User', $user->id, $old, $request->validated());
        return redirect()->route('users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function deactivate(User $user)
    {
        $this->authorize('update', $user);
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        $action = $user->status === 'active' ? 'activated' : 'deactivated';
        AuditLogService::log('updated', 'User', 'User', $user->id, null, ['status' => $user->status]);
        return back()->with('success', "User '{$user->name}' has been {$action}.");
    }

    public function resetPassword(User $user)
    {
        $this->authorize('update', $user);
        $tempPassword = Str::random(12);
        $user->update(['password' => Hash::make($tempPassword)]);
        AuditLogService::log('updated', 'User', 'User', $user->id, null, ['action' => 'password_reset']);
        return back()->with('success', "Password reset for '{$user->name}'. Temporary password: {$tempPassword}");
    }
}
