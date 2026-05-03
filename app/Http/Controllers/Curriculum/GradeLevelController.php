<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\DataTables\Curriculum\GradeLevelDataTable;
use App\Http\Requests\StoreGradeLevelRequest;
use App\Http\Requests\UpdateGradeLevelRequest;
use App\Models\GradeLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        return view('gradelevels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeLevelRequest $request)
    {
        try {
            $gradeLevel = DB::transaction(function () use ($request) {
                $gradeLevel = GradeLevel::create($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($gradeLevel)
                    ->withProperties([
                        'attributes' => [
                            'name'      => $gradeLevel->name,
                            'level'     => $gradeLevel->level,
                            'is_active' => $gradeLevel->is_active,
                        ]
                    ])
                    ->log('Created grade level');

                return $gradeLevel;
            });

            return redirect()
                ->route('gradelevels.show', $gradeLevel)
                ->with('success', 'Grade Level created successfully.');
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
                ->log('Failed to create grade level');

            return back()
                ->withInput()
                ->with('error', 'Failed to create grade level. Please try again.');
        }
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
        try {
            DB::transaction(function () use ($request, $gradeLevel) {
                $old = [
                    'name'      => $gradeLevel->name,
                    'level'     => $gradeLevel->level,
                    'is_active' => $gradeLevel->is_active,
                ];

                $gradeLevel->update($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($gradeLevel)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'name'      => $gradeLevel->name,
                            'level'     => $gradeLevel->level,
                            'is_active' => $gradeLevel->is_active,
                        ]
                    ])
                    ->log('Updated grade level');
            });

            return redirect()
                ->route('gradelevels.show', $gradeLevel)
                ->with('success', 'Grade Level updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($gradeLevel)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update grade level');

            return back()
                ->withInput()
                ->with('error', 'Failed to update grade level. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeLevel $gradeLevel)
    {
        try {
            $snapshot = [
                'name'      => $gradeLevel->name,
                'level'     => $gradeLevel->level,
                'is_active' => $gradeLevel->is_active,
            ];

            DB::transaction(function () use ($gradeLevel, $snapshot) {
                $gradeLevel->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $snapshot
                    ])
                    ->log('Deleted grade level');
            });

            return redirect()
                ->route('gradelevels.index')
                ->with('success', "Grade Level '{$gradeLevel->name}' deleted successfully.");
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
                ->log('Failed to delete grade level');

            return redirect()
                ->route('gradelevels.index')
                ->with('error', 'Failed to delete grade level. Please try again.');
        }
    }
}
