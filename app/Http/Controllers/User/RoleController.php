<?php

namespace App\Http\Controllers\User;

use App\DataTables\User\RoleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view roles')->only(['index', 'show']);
        $this->middleware('permission:create roles')->only(['create', 'store']);
        $this->middleware('permission:edit roles')->only(['edit', 'update']);
        $this->middleware('permission:delete roles')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();

                $role = Role::create([
                    'name'       => $data['name'],
                    'guard_name' => $data['guard_name'] ?? 'web',
                ]);

                $role->syncPermissions($data['permissions'] ?? []);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($role)
                    ->withProperties([
                        'attributes' => [
                            'name'        => $role->name,
                            'guard_name'  => $role->guard_name,
                            'permissions' => $role->getPermissionNames(),
                        ]
                    ])
                    ->log('Created role');

                return $role;
            });

            return redirect()->route('roles.index')->with('success', 'Role created successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to create role');

            return back()
                ->withInput()
                ->with('error', 'Failed to create role. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $permissions = Permission::all();

        return view('roles.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            $oldPermissions = $role->getPermissionNames();

            DB::transaction(function () use ($request, $role, $oldPermissions) {
                $data = $request->validated();
                $role->update(['name' => $data['name'], 'guard_name' => $data['guard_name'] ?? 'web']);

                // Resolve permissions explicitly by ID and guard
                $permissions = Permission::whereIn('id', $data['permissions'] ?? [])
                    ->where('guard_name', $role->guard_name)
                    ->get();

                $role->syncPermissions($permissions);

                // Clear Spatie's permission cache
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($role)
                    ->withProperties([
                        'old' => [
                            'name'        => $role->getOriginal('name'),
                            'guard_name'  => $role->getOriginal('guard_name'),
                            'permissions' => $oldPermissions,
                        ],
                        'attributes' => [
                            'name'        => $role->name,
                            'guard_name'  => $role->guard_name,
                            'permissions' => $role->getPermissionNames(),
                        ]
                    ])
                    ->log('Updated role');
            });

            return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($role)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update role');

            return back()->withInput()->with('error', 'Failed to update role. Please try again.');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting a role that still has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Cannot delete '{$role->name}' — it still has {$role->users()->count()} user(s) assigned.");
        }

        $roleName        = $role->name;
        $roleGuard       = $role->guard_name;
        $rolePermissions = $role->getPermissionNames();

        try {
            DB::transaction(function () use ($role, $roleName, $roleGuard, $rolePermissions) {
                $role->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => [
                            'name'        => $roleName,
                            'guard_name'  => $roleGuard,
                            'permissions' => $rolePermissions,
                        ]
                    ])
                    ->log('Deleted role');
            });

            return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'name'  => $roleName,
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to delete role');

            return redirect()->route('roles.index')
                ->with('error', "Failed to delete '{$roleName}'. Please try again.");
        }
    }
}
