<?php

namespace App\Http\Controllers\Curriculum;

use App\DataTables\Curriculum\SubjectDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view subjects')->only(['index', 'show']);
        $this->middleware('permission:create subjects')->only(['create', 'store']);
        $this->middleware('permission:edit subjects')->only(['edit', 'update']);
        $this->middleware('permission:delete subjects')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SubjectDataTable $dataTable)
    {
        return $dataTable->render('subject.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subject.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
        try {
            $subject = DB::transaction(function () use ($request) {
                $subject = Subject::create($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->withProperties([
                        'attributes' => [
                            'name'        => $subject->name,
                            'code'        => $subject->code,
                            'description' => $subject->description,
                            'is_active'   => $subject->is_active,
                        ]
                    ])
                    ->log('Created subject');

                return $subject;
            });

            return redirect()
                ->route('subjects.show', $subject)
                ->with('success', 'Subject created successfully.');
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
                ->log('Failed to create subject');

            return back()
                ->withInput()
                ->with('error', 'Failed to create subject. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        return view('subject.show', compact('subject'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('subject.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        try {
            DB::transaction(function () use ($request, $subject) {
                $old = [
                    'name'        => $subject->name,
                    'code'        => $subject->code,
                    'description' => $subject->description,
                    'is_active'   => $subject->is_active,
                ];

                $subject->update($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($subject)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => [
                            'name'        => $subject->name,
                            'code'        => $subject->code,
                            'description' => $subject->description,
                            'is_active'   => $subject->is_active,
                        ]
                    ])
                    ->log('Updated subject');
            });

            return redirect()
                ->route('subjects.show', $subject)
                ->with('success', 'Subject updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($subject)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update subject');

            return back()
                ->withInput()
                ->with('error', 'Failed to update subject. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        try {
            $snapshot = [
                'name'        => $subject->name,
                'code'        => $subject->code,
                'description' => $subject->description,
                'is_active'   => $subject->is_active,
            ];

            DB::transaction(function () use ($subject, $snapshot) {
                $subject->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $snapshot
                    ])
                    ->log('Deleted subject');
            });

            return redirect()
                ->route('subjects.index')
                ->with('success', "Subject '{$subject->name}' deleted successfully.");
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
                ->log('Failed to delete subject');

            return redirect()
                ->route('subjects.index')
                ->with('error', 'Failed to delete subject. Please try again.');
        }
    }
}
