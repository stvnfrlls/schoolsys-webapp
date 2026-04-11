<?php

namespace App\Http\Controllers\User;

use App\DataTables\User\UserDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view users')->only(['index', 'show']);
        $this->middleware('permission:create users')->only(['create', 'store']);
        $this->middleware('permission:edit users')->only(['edit', 'update', 'updatePassword']);
        $this->middleware('permission:delete users')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = DB::transaction(function () use ($request) {

                $data = $request->validated();

                $user = User::create([
                    ...collect($data)->except('role')->toArray(),
                    'password' => Hash::make($data['password']),
                ]);

                $user->assignRole($data['role']);

                return $user;
            });

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();

        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::transaction(function () use ($request, $user) {

                $data = $request->validated();

                $user->update(
                    collect($data)->except('role')->toArray()
                );

                $user->syncRoles($data['role']);
            });

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                $user->delete();
            });

            return redirect()
                ->route('users.index')
                ->with('success', "User '{$user->name}' deleted successfully.");
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('users.index')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }
}
