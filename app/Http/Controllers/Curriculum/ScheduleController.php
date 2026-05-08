<?php

namespace App\Http\Controllers\Curriculum;

use App\DataTables\Curriculum\ScheduleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view schedules')->only(['index', 'show']);
        $this->middleware('permission:create schedules')->only(['create', 'store']);
        $this->middleware('permission:edit schedules')->only(['edit', 'update']);
        $this->middleware('permission:delete schedules')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ScheduleDataTable $dataTable)
    {
        return $dataTable->render('schedules.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $schoolYears = SchoolYear::orderByDesc('created_at')->get();
        $sections    = Section::with('gradeLevel')->orderBy('name')->get();
        $subjects    = Subject::orderBy('name')->get();
        $faculties   = Faculty::where('status', 'active')
            ->orderBy('last_name')
            ->get();

        return view('schedules.create', compact(
            'schoolYears',
            'sections',
            'subjects',
            'faculties',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            $schedule = DB::transaction(function () use ($request) {
                $schedule = Schedule::create($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($schedule)
                    ->withProperties([
                        'attributes' => $this->snapshot($schedule),
                    ])
                    ->log('Created schedule');

                return $schedule;
            });

            return redirect()
                ->route('schedules.show', $schedule)
                ->with('success', 'Schedule created successfully.');
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
                ->log('Failed to create schedule');

            return back()
                ->withInput()
                ->with('error', 'Failed to create schedule. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load('schoolYear', 'section.gradeLevel', 'subject', 'faculty');

        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $schedule->load('schoolYear', 'section.gradeLevel', 'subject', 'faculty');

        $schoolYears = SchoolYear::orderByDesc('created_at')->get();
        $sections    = Section::with('gradeLevel')->orderBy('name')->get();
        $subjects    = Subject::orderBy('name')->get();
        $faculties   = Faculty::where('status', 'active')
            ->orderBy('last_name')
            ->get();

        return view('schedules.edit', compact(
            'schedule',
            'schoolYears',
            'sections',
            'subjects',
            'faculties',
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        try {
            DB::transaction(function () use ($request, $schedule) {
                $old = $this->snapshot($schedule);

                $schedule->update($request->validated());

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($schedule)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => $this->snapshot($schedule),
                    ])
                    ->log('Updated schedule');
            });

            return redirect()
                ->route('schedules.show', $schedule)
                ->with('success', 'Schedule updated successfully.');
        } catch (\Throwable $e) {
            report($e);

            activity()
                ->causedBy(Auth::user())
                ->performedOn($schedule)
                ->withProperties([
                    'attributes' => [
                        'error' => $e->getMessage(),
                        'file'  => $e->getFile(),
                        'line'  => (string) $e->getLine(),
                    ]
                ])
                ->log('Failed to update schedule');

            return back()
                ->withInput()
                ->with('error', 'Failed to update schedule. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $snapshot = $this->snapshot($schedule);

            DB::transaction(function () use ($schedule, $snapshot) {
                $schedule->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'old' => $snapshot,
                    ])
                    ->log('Deleted schedule');
            });

            return redirect()
                ->route('schedules.index')
                ->with('success', 'Schedule deleted successfully.');
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
                ->log('Failed to delete schedule');

            return redirect()
                ->route('schedules.index')
                ->with('error', 'Failed to delete schedule. Please try again.');
        }
    }

    /**
     * Build a consistent activity log snapshot for a schedule record.
     */
    private function snapshot(Schedule $schedule): array
    {
        return [
            'school_year_id' => $schedule->school_year_id,
            'section_id'     => $schedule->section_id,
            'subject_id'     => $schedule->subject_id,
            'faculty_id'     => $schedule->faculty_id,
            'day_of_week'    => $schedule->day_of_week,
            'time_start'     => $schedule->time_start,
            'time_end'       => $schedule->time_end,
            'room'           => $schedule->room,
        ];
    }
}
