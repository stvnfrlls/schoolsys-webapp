<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\Subject;
use App\Models\SubjectPerLevel;
use App\DataTables\Curriculum\SubjectPerLevelDataTable;
use App\Http\Requests\StoreSubjectPerLevelRequest;
use App\Http\Requests\UpdateSubjectPerLevelRequest;

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
            'subjects' => Subject::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectPerLevelRequest $request)
    {
        SubjectPerLevel::create($request->validated());

        return redirect()
            ->route('subjectperlevel.index')
            ->with('success', 'Subject assignment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubjectPerLevel $subjectperlevel)
    {
        return view('subjectperlevel.show', compact('subjectperlevel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubjectPerLevel $subjectperlevel)
    {
        $gradeLevels = GradeLevel::all();
        $subjects = Subject::all();

        return view('subjectperlevel.edit', compact('subjectperlevel', 'gradeLevels', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectPerLevelRequest $request, SubjectPerLevel $subjectperlevel)
    {
        $subjectperlevel->update($request->validated());

        return redirect()
            ->route('subjectperlevel.show', $subjectperlevel)
            ->with('success', 'Subject assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubjectPerLevel $subjectperlevel)
    {
        $subjectperlevel->delete();

        return redirect()->route('subjectperlevel.index')->with('success', 'Subject Assignment deleted successfully.');
    }
}
