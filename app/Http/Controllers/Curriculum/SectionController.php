<?php

namespace App\Http\Controllers\Curriculum;

use App\Models\GradeLevel;
use App\Models\Section;
use App\Http\Controllers\Controller;
use App\DataTables\Curriculum\SectionDataTable;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view sections')->only(['index', 'show']);
        $this->middleware('permission:create sections')->only(['create', 'store']);
        $this->middleware('permission:edit sections')->only(['edit', 'update']);
        $this->middleware('permission:delete sections')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SectionDataTable $dataTable)
    {
        return $dataTable->render('sections.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gradeLevels = GradeLevel::all();

        return view('sections.create', compact('gradeLevels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request)
    {
        try {
            $section = DB::transaction(function () use ($request) {
                $section = Section::create($request->validated());

                $section->load('gradeLevel');

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($section)
                    ->withProperties([
                        'attributes' => [
                            'name'        => $section->name,
                            'grade_level' => $section->gradeLevel->name,
                            'is_active'   => $section->is_active,
                        ]
                    ])
                    ->log('Created section');

                return $section;
            });

            return redirect()
                ->route('sections.show', $section)
                ->with('success', 'Section created successfully.');
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
                ->log('Failed to create section');

            return back()
                ->withInput()
                ->with('error', 'Failed to create section. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        $section->load('gradeLevel');

        return view('sections.show', compact('section'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        $gradeLevels = GradeLevel::all();

        return view('sections.edit', compact('section', 'gradeLevels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section)
    {
        try {
            DB::transaction(function () use ($request, $section) {
                $section->load('gradeLevel');

                $old = [
                    'name'        => $section->name,
                    'grade_level' => $section->gradeLevel->name,
                    'is_active'   => $section->is_active,
                ];

                $section->update($request->validated());

                $section->load('gradeLevel'); // reload in case grade_level_id changed

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($section)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'name'        => $section->name,
                            'grade_level' => $section->gradeLevel->name,
                            'is_active'   => $section->is_active,
                        ]
                    ])
                    ->log('Updated section');
            });

            return redirect()
                ->route('sections.show', $section)
                ->with('success', 'Section updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($section)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update section');

            return back()
                ->withInput()
                ->with('error', 'Failed to update section. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        try {
            $section->load('gradeLevel');

            $snapshot = [
                'name'        => $section->name,
                'grade_level' => $section->gradeLevel->name,
                'is_active'   => $section->is_active,
            ];

            DB::transaction(function () use ($section, $snapshot) {
                $section->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $snapshot
                    ])
                    ->log('Deleted section');
            });

            return redirect()
                ->route('sections.index')
                ->with('success', "Section '{$section->name}' deleted successfully.");
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
                ->log('Failed to delete section');

            return redirect()
                ->route('sections.index')
                ->with('error', 'Failed to delete section. Please try again.');
        }
    }
}
