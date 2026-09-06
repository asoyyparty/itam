<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements \Illuminate\Routing\Controllers\HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new \Illuminate\Routing\Controllers\Middleware('permission:action_manage_settings', only: ['create', 'store', 'edit', 'update', 'destroy', 'importExcel', 'updateStatus', 'complete', 'generateTag', 'returnAsset', 'pingBatch', 'ping', 'updateAll']),
        ];
    }

    public function index(Request $request)
    {
        $baseQuery = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Super Admin');
        });

        // Honest IAM telemetry counts (computed across active manageable users)
        $totalUsersCount = (clone $baseQuery)->count();
        $verifiedUsersCount = (clone $baseQuery)->whereNotNull('email_verified_at')->count();
        $rolesWithCount = Role::where('name', '!=', 'Super Admin')
            ->withCount(['users' => function ($q) {
                $q->whereDoesntHave('roles', function ($r) {
                    $r->where('name', 'Super Admin');
                });
            }])
            ->get();

        $query = clone $baseQuery;

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->has('role_id') && $request->role_id != '') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('id', $request->role_id);
            });
        }

        $users = $query->orderBy('name')->get();
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('users.index', compact('users', 'roles', 'totalUsersCount', 'verifiedUsersCount', 'rolesWithCount'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($request->role);

            return redirect()->route('users.index', request()->query())->with('success', __('messages.created_success') ?? 'User created successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create user: '.$e->getMessage());
        }
    }

    public function edit(User $user)
    {
        // Protect from editing Super Admin
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Role::where('name', '!=', 'Super Admin')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        if ($user->hasRole('Super Admin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles([$request->role]);

            return redirect()->route('users.index', request()->query())->with('success', __('messages.updated_success') ?? 'User updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update user: '.$e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Super Admin') || $user->id === auth()->id()) {
            return back()->with('error', __('messages.cannot_delete_user') ?? 'Cannot delete this user.');
        }

        try {
            $user->delete();

            return redirect()->route('users.index', request()->query())->with('success', __('messages.user_deleted_success') ?? __('messages.deleted_success'));
        } catch (\Exception $e) {
            return back()->with('error', __('messages.user_delete_failed') ?? 'Failed to delete user.');
        }
    }
}
