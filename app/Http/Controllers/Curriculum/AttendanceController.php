<?php

namespace App\Http\Controllers\Curriculum;

use App\DataTables\Curriculum\AttendanceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SchoolYear;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view attendance')->only(['index', 'summary', 'loadSchedules']);
        $this->middleware('permission:create attendance')->only(['take', 'store']);
        $this->middleware('permission:edit attendance')->only(['edit', 'update']);
        $this->middleware('permission:delete attendance')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(AttendanceDataTable $dataTable)
    {
        $sections = $this->visibleSections();

        return $dataTable->render('attendance.index', compact('sections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function take(Request $request)
    {
        /** @var \App\Models\User $user */
        $user        = Auth::user();
        $schoolYears = SchoolYear::where('is_active', 'active')->orderByDesc('id')->get();
        $schedule    = null;
        $students    = collect();
        $existing    = collect();
        $date        = $request->date ?? today()->toDateString();

        if ($request->schedule_id) {
            $scheduleQuery = Schedule::query();

            // Faculty can only take attendance for their own schedules
            if ($user->hasRole('Faculty')) {
                /** @var \App\Models\Faculty $faculty */
                $faculty = $user->faculty()->first();
                $scheduleQuery->where('faculty_id', $faculty->getKey());
            }

            $schedule = $scheduleQuery->with([
                'subject',
                'section.gradeLevel',
                'section.enrollments' => fn($q) =>
                $q->where(
                    'school_year_id',
                    fn($sq) => $sq->select('school_year_id')
                        ->from('schedules')
                        ->where('id', $request->schedule_id)
                        ->limit(1)
                )->where('status', 'enrolled'),
                'section.enrollments.student',
            ])->findOrFail($request->schedule_id);

            $students = $schedule->section->enrollments
                ->pluck('student')
                ->sortBy('last_name')
                ->values();

            $existing = Attendance::where('schedule_id', $schedule->id)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('attendance.take', compact(
            'schoolYears',
            'schedule',
            'students',
            'existing',
            'date'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();

        $schedule = Schedule::with([
            'section.enrollments' => fn($q) =>
            $q->where(
                'school_year_id',
                fn($sq) =>
                $sq->select('school_year_id')
                    ->from('schedules')
                    ->where('id', $validated['schedule_id'])
                    ->limit(1)
            )->where('status', 'enrolled'),
        ])->findOrFail($validated['schedule_id']);

        $enrolledStudentIds = $schedule->section->enrollments->pluck('student_id')->toArray();
        $submittedIds       = array_map('intval', array_keys($validated['records']));

        if (! empty(array_diff($submittedIds, $enrolledStudentIds))) {
            return redirect()->back()->withInput()
                ->with('error', 'Invalid student records detected. Please reload and try again.');
        }

        try {
            DB::transaction(function () use ($validated) {
                $now  = now();
                $rows = [];

                foreach ($validated['records'] as $studentId => $status) {
                    $rows[] = [
                        'schedule_id' => $validated['schedule_id'],
                        'student_id'  => (int) $studentId,
                        'date'        => $validated['date'],
                        'status'      => $status,
                        'remarks'     => $validated['remarks'][$studentId] ?? null,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }

                Attendance::upsert(
                    $rows,
                    ['schedule_id', 'student_id', 'date'],
                    ['status', 'remarks', 'updated_at']
                );

                activity()
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'schedule_id' => $validated['schedule_id'],
                        'date'        => $validated['date'],
                        'count'       => count($rows),
                    ])
                    ->log('saved attendance');
            });

            return redirect()
                ->route('attendance.index')
                ->with('success', 'Attendance saved successfully.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->withInput()
                ->with('error', 'Something went wrong while saving attendance. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $attendance->load('student', 'schedule.subject', 'schedule.section');

        return view('attendance.edit', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $attendance) {
                $old = $attendance->only(['status', 'remarks']);

                $attendance->update($validated);

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($attendance)
                    ->withProperties([
                        'old'        => $old,
                        'attributes' => $attendance->fresh()->only(['status', 'remarks']),
                    ])
                    ->log('corrected attendance record');
            });

            return redirect()
                ->route('attendance.index')
                ->with('success', 'Attendance record updated.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        try {
            DB::transaction(function () use ($attendance) {
                $old = $attendance->toArray();
                $attendance->delete();

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($attendance)
                    ->withProperties(['old' => $old])
                    ->log('deleted attendance record');
            });

            return redirect()
                ->route('attendance.index')
                ->with('success', 'Attendance record deleted.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function summary(Request $request)
    {
        /** @var \App\Models\User $user */
        $user         = Auth::user();
        $schoolYears  = SchoolYear::orderByDesc('id')->get();
        $activeYear   = SchoolYear::where('is_active', 'active')->first();
        $schoolYearId = $request->integer('school_year_id', $activeYear?->id);
        $sectionId    = $request->integer('section_id');
        $sections     = $this->visibleSections();
        $summaries    = collect();

        if ($schoolYearId) {
            $query = Attendance::select(
                'attendances.student_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN attendances.status IN ('present','late') THEN 1 ELSE 0 END) as attended"),
                DB::raw("SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'late'    THEN 1 ELSE 0 END) as late_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'absent'  THEN 1 ELSE 0 END) as absent_count"),
                DB::raw("SUM(CASE WHEN attendances.status = 'excused' THEN 1 ELSE 0 END) as excused_count")
            )
                ->join('schedules', 'schedules.id', '=', 'attendances.schedule_id')
                ->where('schedules.school_year_id', $schoolYearId)
                ->when($sectionId, fn($q) => $q->where('schedules.section_id', $sectionId));

            // ── Scope summary rows per role ───────────────────────
            if ($user->hasRole('Faculty')) {
                /** @var \App\Models\Faculty $faculty */
                $faculty = $user->faculty()->first();
                $query->where('schedules.faculty_id', $faculty->getKey());
            }

            if ($user->hasRole('Student')) {
                /** @var \App\Models\Student $student */
                $student = $user->student()->first();
                $query->where('attendances.student_id', $student->getKey());
            }

            $summaries = $query
                ->groupBy('attendances.student_id')
                ->with('student')
                ->get()
                ->map(fn($row) => [
                    'student'       => $row->student,
                    'total'         => $row->total,
                    'attended'      => $row->attended,
                    'present_count' => $row->present_count,
                    'late_count'    => $row->late_count,
                    'absent_count'  => $row->absent_count,
                    'excused_count' => $row->excused_count,
                    'rate'          => $row->total > 0
                        ? round(($row->attended / $row->total) * 100, 1)
                        : 0.0,
                ])
                ->sortBy('student.last_name')
                ->values();
        }

        return view('attendance.summary', compact(
            'schoolYears',
            'schoolYearId',
            'sectionId',
            'sections',
            'summaries'
        ));
    }

    public function loadSchedules(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'school_year_id' => ['required', 'integer', 'exists:school_years,id'],
            'date'           => ['nullable', 'date'],
        ]);

        $query = Schedule::with(['subject', 'section'])
            ->where('school_year_id', $request->school_year_id);

        // Filter to the day of week matching the selected date
        if ($request->date) {
            $dayOfWeek = Carbon::parse($request->date)->isoWeekday(); // 1=Mon … 7=Sun

            if ($dayOfWeek > 5) {
                // Weekend — no schedules possible
                return response()->json([]);
            }

            $query->where('day_of_week', $dayOfWeek);
        }

        // Faculty only sees their own schedules
        if ($user->hasRole('Faculty')) {
            /** @var \App\Models\Faculty $faculty */
            $faculty = $user->faculty()->first();
            $query->where('faculty_id', $faculty->getKey());
        }

        $schedules = $query->get()->map(fn($s) => [
            'id'           => $s->id,
            'subject_name' => $s->subject->name ?? '—',
            'section_name' => $s->section->name ?? '—',
            'day_of_week'  => $s->day_of_week,
            'time_start'   => Carbon::parse($s->time_start)->format('g:i A'),
        ]);

        return response()->json($schedules);
    }

    private function visibleSections()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $base = Section::with('gradeLevel')->where('is_active', 'active');

        if ($user->hasRole('Admin')) {
            return $base->get();
        }

        if ($user->hasRole('Faculty')) {
            /** @var \App\Models\Faculty $faculty */
            $faculty    = $user->faculty()->first();
            $sectionIds = Schedule::where('faculty_id', $faculty->getKey())
                ->pluck('section_id')
                ->unique();

            return $base->whereIn('id', $sectionIds)->get();
        }

        if ($user->hasRole('Student')) {
            /** @var \App\Models\Student $student */
            $student    = $user->student()->first();
            $sectionIds = $student->enrollments()->pluck('section_id')->unique();

            return $base->whereIn('id', $sectionIds)->get();
        }

        return collect();
    }
}
