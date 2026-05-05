<?php

namespace App\Http\Controllers\User;

use App\DataTables\User\StudentDataTable;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view students')->only(['index', 'show']);
        $this->middleware('permission:create students')->only(['create', 'store']);
        $this->middleware('permission:edit students')->only(['edit', 'update']);
        $this->middleware('permission:delete students')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(StudentDataTable $dataTable)
    {
        return $dataTable->render('students.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        try {
            $student = DB::transaction(function () use ($request) {
                $data = $request->validated();

                $user = User::create([
                    'name'     => trim("{$data['first_name']} {$data['last_name']}"),
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                    'status'   => 'active',
                ]);

                $user->assignRole('student');

                $student = Student::create([
                    'user_id'                => $user->id,
                    'student_number'         => $data['student_number'],
                    'first_name'             => $data['first_name'],
                    'middle_name'            => $data['middle_name'] ?? null,
                    'last_name'              => $data['last_name'],
                    'birth_date'             => $data['birth_date'] ?? null,
                    'gender'                 => $data['gender'] ?? null,
                    'address'                => $data['address'] ?? null,
                    'contact_number'         => $data['contact_number'] ?? null,
                    'guardian_name'          => $data['guardian_name'] ?? null,
                    'guardian_contact'       => $data['guardian_contact'] ?? null,
                    'guardian_relationship'  => $data['guardian_relationship'] ?? null,
                    'status'                 => $data['status'] ?? 'enrolled',
                ]);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($student)
                    ->withProperties([
                        'attributes' => [
                            'name'           => $student->full_name,
                            'student_number' => $student->student_number,
                            'email'          => $user->email,
                        ]
                    ])
                    ->log('Created student');

                return $student;
            });

            return redirect()
                ->route('students.show', $student)
                ->with('success', 'Student created successfully.');
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
                ->log('Failed to create student');

            return back()
                ->withInput()
                ->with('error', 'Failed to create student. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        try {
            DB::transaction(function () use ($request, $student) {
                $data = $request->validated();

                $old = [
                    'name'           => $student->full_name,
                    'student_number' => $student->student_number,
                    'status'         => $student->status,
                ];

                $student->update([
                    'first_name'             => $data['first_name'],
                    'middle_name'            => $data['middle_name'] ?? null,
                    'last_name'              => $data['last_name'],
                    'birth_date'             => $data['birth_date'] ?? null,
                    'gender'                 => $data['gender'] ?? null,
                    'address'                => $data['address'] ?? null,
                    'contact_number'         => $data['contact_number'] ?? null,
                    'guardian_name'          => $data['guardian_name'] ?? null,
                    'guardian_contact'       => $data['guardian_contact'] ?? null,
                    'guardian_relationship'  => $data['guardian_relationship'] ?? null,
                    'status'                 => $data['status'],
                ]);

                if ($student->user) {
                    $student->user->update([
                        'name' => trim("{$data['first_name']} {$data['last_name']}"),
                    ]);
                }

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($student)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'name'           => $student->full_name,
                            'student_number' => $student->student_number,
                            'status'         => $student->status,
                        ]
                    ])
                    ->log('Updated student');
            });

            return redirect()
                ->route('students.show', $student)
                ->with('success', 'Student updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($student)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update student');

            return back()
                ->withInput()
                ->with('error', 'Failed to update student. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        try {
            $snapshot = [
                'name'           => $student->full_name,
                'student_number' => $student->student_number,
            ];

            DB::transaction(function () use ($student, $snapshot) {
                $student->user?->delete();

                $student->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties(['old' => $snapshot])
                    ->log('Deleted student');
            });

            return redirect()
                ->route('students.index')
                ->with('success', "Student '{$snapshot['name']}' deleted successfully.");
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
                ->log('Failed to delete student');

            return redirect()
                ->route('students.index')
                ->with('error', 'Failed to delete student. Please try again.');
        }
    }
}
