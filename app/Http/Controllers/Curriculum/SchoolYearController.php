<?php

namespace App\Http\Controllers\Curriculum;

use App\DataTables\Curriculum\SchoolYearDataTable;
use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Http\Requests\StoreSchoolYearRequest;
use App\Http\Requests\UpdateSchoolYearRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SchoolYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view school years')->only(['index', 'show']);
        $this->middleware('permission:create school years')->only(['create', 'store']);
        $this->middleware('permission:edit school years')->only(['edit', 'update']);
        $this->middleware('permission:delete school years')->only(['destroy']);
    }

    public function index(SchoolYearDataTable $dataTable)
    {
        return $dataTable->render('schoolyears.index');
    }

    public function create(): View
    {
        return view('schoolyears.create');
    }

    public function store(StoreSchoolYearRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            if ($request->input('is_active') === 'active') {
                SchoolYear::query()->update(['is_active' => 'inactive']);
            }

            SchoolYear::create($request->validated());
        });

        return redirect()->route('schoolyears.index')
            ->with('success', 'School year created successfully.');
    }

    public function show(SchoolYear $schoolyear): View
    {
        return view('schoolyears.show', compact('schoolyear'));
    }

    public function edit(SchoolYear $schoolyear): View
    {
        return view('schoolyears.edit', compact('schoolyear'));
    }

    public function update(UpdateSchoolYearRequest $request, SchoolYear $schoolyear): RedirectResponse
    {
        DB::transaction(function () use ($request, $schoolyear) {
            if ($request->input('is_active') === 'active') {
                SchoolYear::query()
                    ->where('id', '!=', $schoolyear->id)
                    ->update(['is_active' => 'inactive']);
            }

            $schoolyear->update($request->validated());
        });

        return redirect()->route('schoolyears.index')
            ->with('success', 'School year updated successfully.');
    }

    public function destroy(SchoolYear $schoolyear): RedirectResponse
    {
        if ($schoolyear->is_active === 'active') {
            return redirect()->route('schoolyears.index')
                ->with('error', 'Cannot delete the active school year.');
        }

        $schoolyear->delete();

        return redirect()->route('schoolyears.index')
            ->with('success', 'School year deleted successfully.');
    }
}
