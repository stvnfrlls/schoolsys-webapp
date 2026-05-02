<?php

namespace App\Http\Controllers\Curriculum;

use App\Models\GradeLevel;
use App\Models\Section;
use App\Http\Controllers\Controller;
use App\DataTables\Curriculum\SectionDataTable;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;

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
        Section::create($request->validated());

        return redirect()->route('sections.index')->with('success', 'Section successfully created.');
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
        $section->update($request->validated());

        return redirect()->route('sections.index')->with('success', 'Section successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Section successfully deleted.');
    }
}
