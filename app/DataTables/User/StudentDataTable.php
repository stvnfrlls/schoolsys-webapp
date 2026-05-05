<?php

namespace App\DataTables\User;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
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
        return (new EloquentDataTable($query))
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('middle_name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn(
                'student_number',
                fn($query, $keyword) =>
                $query->where('student_number', 'like', "%{$keyword}%")
            )
            ->addColumn('full_name', fn(Student $student) => $student->full_name)
            ->editColumn('status', fn(Student $student) => view('components.status-badge', [
                'status' => $student->status,
            ])->render())
            ->editColumn('created_at', fn(Student $student) => $student->created_at->format('M d, Y'))
            ->addColumn(
                'action',
                fn(Student $student) =>
                view('components.actions', [
                    'canView'      => true,
                    'canEdit'      => true,
                    'canDelete'    => true,
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
        return $model->newQuery();
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
                ['responsivePriority' => 4, 'targets' => 3], // status
                ['responsivePriority' => 5, 'targets' => 4], // created_at
                ['responsivePriority' => 6, 'targets' => 0], // id — hides last
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
            Column::make('id')->title('#')->width(50)->searchable(false),
            Column::make('full_name')->title('Name'),
            Column::make('student_number')->title('Student No.'),
            Column::make('status')->title('Status')->searchable(false)->orderable(false),
            Column::make('created_at')->title('Enrolled')->searchable(false),
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
