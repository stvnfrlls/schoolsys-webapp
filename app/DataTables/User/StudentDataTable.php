<?php

namespace App\DataTables\User;

use App\Models\Enrollment;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StudentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Student> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'ilike', "%{$keyword}%")
                        ->orWhere('last_name',   'ilike', "%{$keyword}%")
                        ->orWhere('middle_name', 'ilike', "%{$keyword}%");
                });
            })
            ->filterColumn(
                'student_number',
                fn($query, $keyword) =>
                $query->where('student_number', 'ilike', "%{$keyword}%")
            )
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('currentEnrollment.section', function ($q) use ($keyword) {
                    $q->where('name', 'ilike', "%{$keyword}%");
                });
            })
            ->filterColumn('grade', function ($query, $keyword) {
                $query->whereHas('currentEnrollment.section.gradeLevel', function ($q) use ($keyword) {
                    $q->where('name', 'ilike', "%{$keyword}%");
                });
            })
            ->addColumn('full_name', fn(Student $student) => $student->full_name)
            ->addColumn(
                'section',
                fn(Student $student) =>
                $student->currentEnrollment?->section?->name ?? '—'
            )
            ->addColumn(
                'grade',
                fn(Student $student) =>
                $student->currentEnrollment?->section?->gradeLevel?->name ?? '—'
            )
            ->editColumn('status', fn(Student $student) => view('components.status-badge', [
                'status' => $student->status,
            ])->render())
            ->editColumn('created_at', fn(Student $student) => $student->created_at->format('M d, Y'))
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("TO_CHAR(created_at, 'Mon DD, YYYY') ILIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn(
                'action',
                fn(Student $student) =>
                view('components.actions', [
                    'canView'      => $authUser->hasPermissionTo('view students'),
                    'canEdit'      => $authUser->hasPermissionTo('edit students'),
                    'canDelete'    => $authUser->hasPermissionTo('delete students'),
                    'routeKeyName' => 'students.',
                    'param'        => $student,
                ])->render()
            )
            ->rawColumns(['action', 'status'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Student>
     */
    public function query(Student $model): QueryBuilder
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Eager load currentEnrollment → section → gradeLevel to avoid N+1
        $baseQuery = $model->newQuery()->with('currentEnrollment.section.gradeLevel');

        if ($user->hasRole(['Admin'])) {
            return $baseQuery;
        }

        if ($user->hasRole('Faculty')) {
            /** @var \App\Models\Faculty $faculty */
            $faculty = $user->faculty()->first();

            $sectionIds = Schedule::where('faculty_id', $faculty->id)
                ->pluck('section_id')
                ->unique();

            $studentIds = Enrollment::whereIn('section_id', $sectionIds)
                ->whereHas('schoolYear', fn($q) => $q->where('is_active', 'active'))
                ->pluck('student_id')
                ->unique();

            return $baseQuery->whereIn('id', $studentIds);
        }

        if ($user->hasRole('Student')) {
            /** @var \App\Models\Student $student */
            $student = $user->student()->first();

            return $baseQuery->where('id', $student->id);
        }

        return $baseQuery->whereRaw('1 = 0');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('students-table')
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
                ['responsivePriority' => 1, 'targets' => 1], // full_name
                ['responsivePriority' => 2, 'targets' => -1], // action
                ['responsivePriority' => 3, 'targets' => 2], // student_number
                ['responsivePriority' => 4, 'targets' => 3], // grade
                ['responsivePriority' => 5, 'targets' => 4], // section
                ['responsivePriority' => 6, 'targets' => 5], // status
                ['responsivePriority' => 7, 'targets' => 6], // created_at
                ['responsivePriority' => 8, 'targets' => 0], // id — hides last
            ])
            ->layout([
                'topStart'    => null,
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->orderBy(1, 'asc')
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
            Column::make('student_number')->title('Student No.'),
            Column::make('full_name')->title('Name'),
            Column::computed('grade')->title('Grade')->searchable(true)->orderable(false),
            Column::computed('section')->title('Section')->searchable(true)->orderable(false),
            Column::make('status')->title('Status')->searchable(false)->orderable(false),
            Column::make('created_at')->title('Enrolled')->searchable(true),
            Column::computed('action')->title('Actions')->exportable(false)->printable(false)->searchable(false)->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Student_' . date('YmdHis');
    }
}
