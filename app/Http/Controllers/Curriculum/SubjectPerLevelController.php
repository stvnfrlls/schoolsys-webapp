<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\SubjectPerLevel;
use App\DataTables\Curriculum\SubjectPerLevelDataTable;
use App\Http\Requests\StoreSubjectPerLevelRequest;
use App\Http\Requests\UpdateSubjectPerLevelRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectPerLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view subject per level')->only(['index', 'show']);
        $this->middleware('permission:create subject per level')->only(['create', 'store']);
        $this->middleware('permission:edit subject per level')->only(['edit', 'update']);
        $this->middleware('permission:delete subject per level')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SubjectPerLevelDataTable $dataTable)
    {
        return $dataTable->render('subjectperlevel.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subjectperlevel.create', [
            'gradeLevels' => GradeLevel::all(),
            'subjects'    => Subject::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectPerLevelRequest $request)
    {
        try {
            $subjectperlevel = DB::transaction(function () use ($request) {
                $subjectperlevel = SubjectPerLevel::create($request->validated());

                $subjectperlevel->load('gradeLevel', 'subject');

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subjectperlevel)
                    ->withProperties([
                        'attributes' => [
                            'grade_level'   => $subjectperlevel->gradeLevel->name,
                            'subject'       => $subjectperlevel->subject->name,
                            'hours_per_week' => $subjectperlevel->hours_per_week,
                            'is_active'     => $subjectperlevel->is_active,
                        ]
                    ])
                    ->log('Created subject assignment');

                return $subjectperlevel;
            });

            return redirect()
                ->route('subjectperlevel.show', $subjectperlevel)
                ->with('success', 'Subject assignment created successfully.');
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
                ->log('Failed to create subject assignment');

            return back()
                ->withInput()
                ->with('error', 'Failed to create subject assignment. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SubjectPerLevel $subjectperlevel)
    {
        $subjectperlevel->load('gradeLevel', 'subject');

        return view('subjectperlevel.show', compact('subjectperlevel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubjectPerLevel $subjectperlevel)
    {
        $gradeLevels = GradeLevel::all();
        $subjects    = Subject::all();

        return view('subjectperlevel.edit', compact('subjectperlevel', 'gradeLevels', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectPerLevelRequest $request, SubjectPerLevel $subjectperlevel)
    {
        try {
            DB::transaction(function () use ($request, $subjectperlevel) {
                $subjectperlevel->load('gradeLevel', 'subject');

                $old = [
                    'grade_level'    => $subjectperlevel->gradeLevel->name,
                    'subject'        => $subjectperlevel->subject->name,
                    'hours_per_week' => $subjectperlevel->hours_per_week,
                    'is_active'      => $subjectperlevel->is_active,
                ];

                $subjectperlevel->update($request->validated());

                $subjectperlevel->load('gradeLevel', 'subject'); // reload in case FKs changed

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subjectperlevel)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'grade_level'    => $subjectperlevel->gradeLevel->name,
                            'subject'        => $subjectperlevel->subject->name,
                            'hours_per_week' => $subjectperlevel->hours_per_week,
                            'is_active'      => $subjectperlevel->is_active,
                        ]
                    ])
                    ->log('Updated subject assignment');
            });

            return redirect()
                ->route('subjectperlevel.show', $subjectperlevel)
                ->with('success', 'Subject assignment updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subjectperlevel)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update subject assignment');

            return back()
                ->withInput()
                ->with('error', 'Failed to update subject assignment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubjectPerLevel $subjectperlevel)
    {
        try {
            $subjectperlevel->load('gradeLevel', 'subject');

            $snapshot = [
                'grade_level'    => $subjectperlevel->gradeLevel->name,
                'subject'        => $subjectperlevel->subject->name,
                'hours_per_week' => $subjectperlevel->hours_per_week,
                'is_active'      => $subjectperlevel->is_active,
            ];

            DB::transaction(function () use ($subjectperlevel, $snapshot) {
                $subjectperlevel->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $snapshot
                    ])
                    ->log('Deleted subject assignment');
            });

            return redirect()
                ->route('subjectperlevel.index')
                ->with('success', "Subject assignment '{$subjectperlevel->subject->name}' deleted successfully.");
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
                ->log('Failed to delete subject assignment');

            return redirect()
                ->route('subjectperlevel.index')
                ->with('error', 'Failed to delete subject assignment. Please try again.');
        }
    }
}
