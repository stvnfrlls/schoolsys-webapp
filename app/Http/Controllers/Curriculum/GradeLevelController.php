<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\DataTables\Curriculum\GradeLevelDataTable;
use App\Http\Requests\StoreGradeLevelRequest;
use App\Http\Requests\UpdateGradeLevelRequest;
use App\Models\GradeLevel;

class GradeLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view grade levels')->only(['index', 'show']);
        $this->middleware('permission:create grade levels')->only(['create', 'store']);
        $this->middleware('permission:edit grade levels')->only(['edit', 'update']);
        $this->middleware('permission:delete grade levels')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(GradeLevelDataTable $dataTable)
    {
        return $dataTable->render('gradelevels.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gradeLevel = GradeLevel::all();

        return view('gradelevels.create', compact('gradeLevel'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeLevelRequest $request)
    {
        GradeLevel::create($request->validated());

        return redirect()->route('gradelevels.index')->with('success', 'Grade Level created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GradeLevel $gradeLevel)
    {
        return view('gradelevels.show', compact('gradeLevel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GradeLevel $gradeLevel)
    {
        return view('gradelevels.edit', compact('gradeLevel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGradeLevelRequest $request, GradeLevel $gradeLevel)
    {
        $gradeLevel->update($request->validated());

        return redirect()->route('gradelevels.index')->with('success', 'Grade Level updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeLevel $gradeLevel)
    {
        $gradeLevel->delete();

        return redirect()->route('gradelevels.index')->with('success', 'Grade Level deleted successfully.');
    }
}
