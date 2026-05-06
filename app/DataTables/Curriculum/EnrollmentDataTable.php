<?php

namespace App\DataTables\Curriculum;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class EnrollmentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Enrollment> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
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
                    'canView'      => true,
                    'canEdit'      => true,
                    'canDelete'    => true,
                    'routeKeyName' => 'enrollments.',
                    'param'        => $enrollment
                ])->render()
            )
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
        return $model->newQuery()->with(['student', 'section', 'schoolYear']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
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
            Column::make('student')->title('Student')->orderable(false),
            Column::make('section')->title('Section')->orderable(false),
            Column::make('school_year')->title('School Year')->orderable(false),
            Column::make('status')->title('Status'),
            Column::make('enrolled_at')->title('Enrolled At'),
            Column::computed('action')
                ->title('Actions')
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
