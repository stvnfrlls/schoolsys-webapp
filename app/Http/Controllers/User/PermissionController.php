<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\DataTables\User\PermissionDataTable;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permissions')->only(['index', 'show']);
        $this->middleware('permission:create permissions')->only(['create', 'store']);
        $this->middleware('permission:edit permissions')->only(['edit', 'update']);
        $this->middleware('permission:delete permissions')->only(['destroy']);
    }
    
    /**
     * Display a listing of permissions (DataTable).
     */
    public function index(PermissionDataTable $dataTable)
    {
        return $dataTable->render('permissions.index');
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        return view('permissions.create');
    }
    /**
     * Store a newly created permission.
     */
    public function store(StorePermissionRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->validated();

                $permission = Permission::create([
                    'name'       => $data['name'],
                    'guard_name' => $data['guard_name'] ?? 'web',
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($permission)
                    ->withProperties([
                        'attributes' => [
                            'name'        => $permission->name,
                            'guard_name'  => $permission->guard_name,
                        ]
                    ])
                    ->log('Created permission');
            });

            return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
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
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the permission.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        try {
            $oldPermission = [
                'name'       => $permission->name,
                'guard_name' => $permission->guard_name,
            ];

            DB::transaction(function () use ($request, $permission, $oldPermission) {
                $data = $request->validated();

                $permission->update([
                    'name'       => $data['name'],
                    'guard_name' => $data['guard_name'],
                ]);

                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($permission)
                    ->withProperties([
                        'old' => [
                            'name'       => $oldPermission['name'],
                            'guard_name' => $oldPermission['guard_name'],
                        ],
                        'attributes' => [
                            'name'       => $permission->name,
                            'guard_name' => $permission->guard_name,
                        ]
                    ])
                    ->log('Updated permission');
            });

            return redirect()
                ->route('permissions.index')
                ->with('success', 'Permission updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($permission)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update permission');

            return back()
                ->withInput()
                ->with('error', 'Failed to update permission. Please try again.');
        }
    }
    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->whereRaw('LOWER(name) = ?', ['admin'])->exists()) {
            return redirect()
                ->route('permissions.index')
                ->with('error', 'This permission is currently assigned to the admin role and cannot be deleted.');
        }

        $permissionName  = $permission->name;
        $permissionGuard = $permission->guard_name;

        try {
            DB::transaction(function () use ($permission, $permissionName, $permissionGuard) {
                $permission->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => [
                            'name'       => $permissionName,
                            'guard_name' => $permissionGuard,
                        ]
                    ])
                    ->log('Deleted permission');
            });

            return redirect()
                ->route('permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'attributes' => [
                        'name'  => $permissionName,
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to delete permission');

            return redirect()
                ->route('permissions.index')
                ->with('error', "Failed to delete '{$permissionName}'. Please try again.");
        }
    }
}
