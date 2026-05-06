<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\DataTables\Curriculum\EnrollmentDataTable;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view enrollments')->only(['index', 'show']);
        $this->middleware('permission:create enrollments')->only(['create', 'store']);
        $this->middleware('permission:edit enrollments')->only(['edit', 'update']);
        $this->middleware('permission:delete enrollments')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EnrollmentDataTable $dataTable)
    {
        return $dataTable->render('enrollments.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students    = Student::orderBy('last_name')->get();
        $sections    = Section::orderBy('name')->get();
        $schoolYears = SchoolYear::orderBy('name', 'desc')->get();

        return view('enrollments.create', compact('students', 'sections', 'schoolYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        try {
            $enrollment = DB::transaction(function () use ($request) {
                $enrollment = Enrollment::create($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($enrollment)
                    ->withProperties([
                        'attributes' => [
                            'student_id'     => $enrollment->student_id,
                            'section_id'     => $enrollment->section_id,
                            'school_year_id' => $enrollment->school_year_id,
                            'status'         => $enrollment->status,
                            'enrolled_at'    => $enrollment->enrolled_at,
                        ]
                    ])
                    ->log('Created enrollment');

                return $enrollment;
            });

            return redirect()
                ->route('enrollments.show', $enrollment)
                ->with('success', 'Enrollment created successfully.');
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
                ->log('Failed to create enrollment');

            return back()
                ->withInput()
                ->with('error', 'Failed to create enrollment. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'section', 'schoolYear']);

        return view('enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Enrollment $enrollment)
    {
        $students    = Student::orderBy('last_name')->get();
        $sections    = Section::orderBy('name')->get();
        $schoolYears = SchoolYear::orderBy('name', 'desc')->get();

        return view('enrollments.edit', compact('enrollment', 'students', 'sections', 'schoolYears'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        try {
            DB::transaction(function () use ($request, $enrollment) {
                $old = [
                    'student_id'     => $enrollment->student_id,
                    'section_id'     => $enrollment->section_id,
                    'school_year_id' => $enrollment->school_year_id,
                    'status'         => $enrollment->status,
                    'enrolled_at'    => $enrollment->enrolled_at,
                ];

                $enrollment->update($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($enrollment)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'student_id'     => $enrollment->student_id,
                            'section_id'     => $enrollment->section_id,
                            'school_year_id' => $enrollment->school_year_id,
                            'status'         => $enrollment->status,
                            'enrolled_at'    => $enrollment->enrolled_at,
                        ]
                    ])
                    ->log('Updated enrollment');
            });

            return redirect()
                ->route('enrollments.show', $enrollment)
                ->with('success', 'Enrollment updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($enrollment)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update enrollment');

            return back()
                ->withInput()
                ->with('error', 'Failed to update enrollment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment)
    {
        try {
            $snapshot = [
                'student_id'     => $enrollment->student_id,
                'section_id'     => $enrollment->section_id,
                'school_year_id' => $enrollment->school_year_id,
                'status'         => $enrollment->status,
                'enrolled_at'    => $enrollment->enrolled_at,
            ];

            // Capture student name before deletion for the flash message
            $studentName = $enrollment->student?->full_name ?? "ID #{$enrollment->student_id}";

            DB::transaction(function () use ($enrollment, $snapshot) {
                $enrollment->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $snapshot
                    ])
                    ->log('Deleted enrollment');
            });

            return redirect()
                ->route('enrollments.index')
                ->with('success', "Enrollment for '{$studentName}' deleted successfully.");
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
                ->log('Failed to delete enrollment');

            return redirect()
                ->route('enrollments.index')
                ->with('error', 'Failed to delete enrollment. Please try again.');
        }
    }
}
