<?php

namespace App\Http\Controllers\User;

use App\DataTables\User\FacultyDataTable;
use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreFacultyRequest;
use App\Http\Requests\UpdateFacultyRequest;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view faculty')->only(['index', 'show']);
        $this->middleware('permission:create faculty')->only(['create', 'store']);
        $this->middleware('permission:edit faculty')->only(['edit', 'update']);
        $this->middleware('permission:delete faculty')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FacultyDataTable $dataTable)
    {
        return $dataTable->render('faculty.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('faculty.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFacultyRequest $request)
    {
        try {
            $faculty = DB::transaction(function () use ($request) {
                $data = $request->validated();

                $user = User::create([
                    'name'     => trim("{$data['first_name']} {$data['last_name']}"),
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                    'status'   => 'active',
                ]);

                $user->assignRole('faculty');

                $faculty = Faculty::create([
                    'user_id'          => $user->id,
                    'employee_number'  => $data['employee_number'],
                    'first_name'       => $data['first_name'],
                    'middle_name'      => $data['middle_name'] ?? null,
                    'last_name'        => $data['last_name'],
                    'birth_date'       => $data['birth_date'] ?? null,
                    'gender'           => $data['gender'] ?? null,
                    'address'          => $data['address'] ?? null,
                    'contact_number'   => $data['contact_number'] ?? null,
                    'department'       => $data['department'] ?? null,
                    'position'         => $data['position'] ?? null,
                    'rank'             => $data['rank'] ?? null,
                    'specialization'   => $data['specialization'] ?? null,
                    'employment_type'  => $data['employment_type'] ?? 'full_time',
                    'status'           => $data['status'] ?? 'active',
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($faculty)
                    ->withProperties([
                        'attributes' => [
                            'name'            => $faculty->full_name,
                            'employee_number' => $faculty->employee_number,
                            'email'           => $user->email,
                        ]
                    ])
                    ->log('Created faculty');

                return $faculty;
            });

            return redirect()
                ->route('faculty.show', $faculty)
                ->with('success', 'Faculty created successfully.');
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
                ->log('Failed to create faculty');

            return back()
                ->withInput()
                ->with('error', 'Failed to create faculty. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faculty $faculty)
    {
        $faculty->load('user');

        return view('faculty.show', compact('faculty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faculty $faculty)
    {
        return view('faculty.edit', compact('faculty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFacultyRequest $request, Faculty $faculty)
    {
        try {
            DB::transaction(function () use ($request, $faculty) {
                $data = $request->validated();

                $old = [
                    'name'            => $faculty->full_name,
                    'employee_number' => $faculty->employee_number,
                    'status'          => $faculty->status,
                ];

                $faculty->update([
                    'first_name'      => $data['first_name'],
                    'middle_name'     => $data['middle_name'] ?? null,
                    'last_name'       => $data['last_name'],
                    'birth_date'      => $data['birth_date'] ?? null,
                    'gender'          => $data['gender'] ?? null,
                    'address'         => $data['address'] ?? null,
                    'contact_number'  => $data['contact_number'] ?? null,
                    'department'      => $data['department'] ?? null,
                    'position'        => $data['position'] ?? null,
                    'rank'            => $data['rank'] ?? null,
                    'specialization'  => $data['specialization'] ?? null,
                    'employment_type' => $data['employment_type'] ?? 'full_time',
                    'status'          => $data['status'],
                ]);

                if ($faculty->user) {
                    $faculty->user->update([
                        'name' => trim("{$data['first_name']} {$data['last_name']}"),
                    ]);
                }

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($faculty)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'name'            => $faculty->full_name,
                            'employee_number' => $faculty->employee_number,
                            'status'          => $faculty->status,
                        ]
                    ])
                    ->log('Updated faculty');
            });

            return redirect()
                ->route('faculty.show', $faculty)
                ->with('success', 'Faculty updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($faculty)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update faculty');

            return back()
                ->withInput()
                ->with('error', 'Failed to update faculty. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faculty $faculty)
    {
        try {
            $snapshot = [
                'name'            => $faculty->full_name,
                'employee_number' => $faculty->employee_number,
            ];

            DB::transaction(function () use ($faculty, $snapshot) {
                $faculty->user?->delete();

                $faculty->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['old' => $snapshot])
                    ->log('Deleted faculty');
            });

            return redirect()
                ->route('faculty.index')
                ->with('success', "Faculty '{$snapshot['name']}' deleted successfully.");
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
                ->log('Failed to delete faculty');

            return redirect()
                ->route('faculty.index')
                ->with('error', 'Failed to delete faculty. Please try again.');
        }
    }
}
