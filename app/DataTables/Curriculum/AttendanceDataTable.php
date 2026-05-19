<?php

namespace App\DataTables\Curriculum;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class AttendanceDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Attendance> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->editColumn(
                'date',
                fn(Attendance $row) =>
                $row->date->format('M d, Y') . ' ' .
                    \Carbon\Carbon::parse($row->schedule->time_start)->format('g:i A')
            )
            ->addColumn(
                'student_name',
                fn(Attendance $row) =>
                $row->student->last_name . ', ' . $row->student->first_name
            )
            ->filterColumn('student_name', function ($query, $keyword) {
                $query->whereHas('student', function ($q) use ($keyword) {
                    $q->whereRaw("LOWER(CONCAT(last_name, ', ', first_name)) LIKE ?", [
                        strtolower("%{$keyword}%")
                    ]);
                });
            })
            ->addColumn(
                'student_number',
                fn(Attendance $row) =>
                $row->student->student_number
            )
            ->addColumn(
                'subject',
                fn(Attendance $row) =>
                $row->schedule->subject->name ?? '—'
            )
            ->filterColumn('subject', function ($query, $keyword) {
                $query->whereHas('schedule.subject', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) LIKE ?', [strtolower("%{$keyword}%")]);
                });
            })
            ->addColumn(
                'section',
                fn(Attendance $row) => ($row->schedule->section->gradeLevel->name ?? '') . ' — ' .
                    ($row->schedule->section->name ?? '—')
            )
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('schedule.section', function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) LIKE ?', [strtolower("%{$keyword}%")]);
                });
            })
            ->editColumn(
                'status',
                fn(Attendance $row) =>
                view('components.status-badge', ['status' => $row->status])->render()
            )->addColumn(
                'action',
                fn(Attendance $row) =>
                view('components.actions', [
                    'canView'      => false,
                    'canEdit'      => $authUser->hasPermissionTo('edit attendance'),
                    'canDelete'    => $authUser->hasPermissionTo('delete attendance'),
                    'routeKeyName' => 'attendance.',
                    'param'        => $row,
                ])->render()
            )
            ->orderColumn('student_name', function ($query, $order) {
                $query->join('students', 'students.id', '=', 'attendances.student_id')
                    ->orderBy('students.last_name', $order)
                    ->orderBy('students.first_name', $order);
            })
            ->orderColumn(
                'date',
                fn($query, $order) =>
                $query->orderBy('attendances.date', $order)
            )
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Attendance>
     */
    public function query(Attendance $model): QueryBuilder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $baseQuery = $model->newQuery()
            ->with(['student', 'schedule.subject', 'schedule.section.gradeLevel'])
            ->when(
                request('section_id'),
                fn($q, $id) => $q->whereHas('schedule', fn($sq) => $sq->where('section_id', $id))
            )
            ->when(request('status'), fn($q, $s) => $q->where('status', $s))
            ->when(request('date'), fn($q, $d) => $q->whereDate('date', $d));

        // ── Admin: sees everything ────────────────────────────────────
        if ($user->hasRole('Admin')) {
            return $baseQuery;
        }

        // ── Faculty: sees attendance for schedules they handle ────────
        if ($user->hasRole('Faculty')) {
            /** @var \App\Models\Faculty $faculty */
            $faculty = $user->faculty()->first();

            return $baseQuery->whereHas(
                'schedule',
                fn($q) => $q->where('faculty_id', $faculty->getKey())
            );
        }

        // ── Student: sees only their own attendance ───────────────────
        if ($user->hasRole('Student')) {
            /** @var \App\Models\Student $student */
            $student = $user->student()->first();

            return $baseQuery->where('student_id', $student->getKey());
        }

        // ── Fallback: sees nothing ────────────────────────────────────
        return $baseQuery->whereRaw('1 = 0');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('attendance-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('attendance.index'), null, [
                'section_id' => "function() { return $('#filter-section').val(); }",
                'status'     => "function() { return $('#filter-status').val(); }",
                'date'       => "function() { return $('#filter-date').val(); }",
            ])
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
                ['responsivePriority' => 1, 'targets' => 1],  // student name
                ['responsivePriority' => 2, 'targets' => -1], // actions
                ['responsivePriority' => 3, 'targets' => 0],  // date
                ['responsivePriority' => 4, 'targets' => 5],  // status
                ['responsivePriority' => 5, 'targets' => 3],  // subject
                ['responsivePriority' => 6, 'targets' => 4],  // section
            ])
            ->layout([
                'topStart'    => null,
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->orderBy(0, 'desc')
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
            Column::make('date')->title('Date'),
            Column::make('student_name')->title('Student'),
            Column::make('student_number')->title('Student No.')->searchable(false)->orderable(false),
            Column::make('subject')->title('Subject')->orderable(false),
            Column::make('section')->title('Section')->orderable(false),
            Column::make('status')->title('Status')->orderable(true)->searchable(false),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Attendance_' . date('YmdHis');
    }
}
