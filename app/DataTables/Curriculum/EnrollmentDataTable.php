<?php

namespace App\DataTables\Curriculum;

use App\Models\Enrollment;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;

class EnrollmentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Enrollment> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->addColumn(
                'student',
                fn(Enrollment $enrollment) => $enrollment->student->full_name ?? '—'
            )
            ->addColumn(
                'section',
                fn(Enrollment $enrollment) => $enrollment->section->name ?? '—'
            )
            ->addColumn(
                'school_year',
                fn(Enrollment $enrollment) => $enrollment->schoolYear->name ?? '—'
            )
            ->addColumn(
                'status',
                fn(Enrollment $enrollment) =>
                view('components.status-badge', ['status' => $enrollment->status])->render()
            )
            ->addColumn(
                'enrolled_at',
                fn(Enrollment $enrollment) =>
                \Carbon\Carbon::parse($enrollment->enrolled_at)->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(Enrollment $enrollment) =>
                view('components.actions', [
                    'canView'      => $authUser->hasPermissionTo('view enrollments'),
                    'canEdit'      => $authUser->hasPermissionTo('edit enrollments'),
                    'canDelete'    => $authUser->hasPermissionTo('delete enrollments'),
                    'routeKeyName' => 'enrollments.',
                    'param'        => $enrollment
                ])->render()
            )
            ->filterColumn('student', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(students.last_name, ' ', students.first_name) ILIKE ?",
                    ["%{$keyword}%"]
                );
            })
            ->filterColumn('section', function ($query, $keyword) {
                $query->where('sections.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('school_year', function ($query, $keyword) {
                $query->where('school_years.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('enrollments.status', 'ilike', "%{$keyword}%");
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Enrollment>
     */
    public function query(Enrollment $model): QueryBuilder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $baseQuery = $model->newQuery()
            ->with(['student', 'section', 'schoolYear'])
            ->join('students', 'students.id', '=', 'enrollments.student_id')
            ->join('sections', 'sections.id', '=', 'enrollments.section_id')
            ->join('school_years', 'school_years.id', '=', 'enrollments.school_year_id')
            ->select('enrollments.*');

        // ── Admin: show all enrollments ───────────────────────────
        if ($user->hasRole('Admin')) {
            return $baseQuery;
        }

        // ── Faculty: show only enrollments in their sections (active school year) ───
        if ($user->hasRole('Faculty')) {
            /** @var \App\Models\Faculty $faculty */
            $faculty = $user->faculty()->first();

            $sectionIds = Schedule::where('faculty_id', $faculty->getKey())
                ->pluck('section_id')
                ->unique();

            return $baseQuery
                ->whereIn('enrollments.section_id', $sectionIds)
                ->whereHas('schoolYear', fn($q) => $q->where('is_active', 'active'));
        }

        // ── Student: show only their own enrollments ──────────────
        if ($user->hasRole('Student')) {
            /** @var \App\Models\Student $student */
            $student = $user->student()->first();

            return $baseQuery->where('enrollments.student_id', $student->getKey());
        }

        // ── Fallback: show nothing ────────────────────────────────
        return $baseQuery->whereRaw('1 = 0');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('enrollments-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->responsive([
                'details' => [
                    'type'   => 'inline',
                    'target' => 'tr',
                ]
            ])
            ->setTableAttribute('dom', 'lrtip')
            ->parameters([
                'lengthChange' => true,
            ])
            ->columnDefs([
                ['responsivePriority' => 1, 'targets' => 0], // Student      — always visible
                ['responsivePriority' => 2, 'targets' => -1], // Actions     — always visible
                ['responsivePriority' => 3, 'targets' => 3], // Status
                ['responsivePriority' => 4, 'targets' => 1], // Section
                ['responsivePriority' => 5, 'targets' => 2], // School Year
                ['responsivePriority' => 6, 'targets' => 4], // Enrolled At  — collapses first
            ])
            ->layout([
                'topStart'    => null,
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->orderBy(0, 'asc')
            ->selectStyleSingle()
            ->searching(true)
            ->pageLength(15)
            ->lengthMenu([[10, 15, 25, 50], [10, 15, 25, 50]]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('student')->title('Student')->searchable(true)->orderable(false),
            Column::computed('section')->title('Section')->searchable(true)->orderable(false),
            Column::computed('school_year')->title('School Year')->searchable(true)->orderable(false),
            Column::computed('status')->title('Status')->searchable(true),
            Column::computed('enrolled_at')->title('Enrolled At')->searchable(false),
            Column::computed('action')
                ->title('Actions')
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Enrollment_' . date('YmdHis');
    }
}
