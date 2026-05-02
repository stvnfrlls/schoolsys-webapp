<?php

namespace App\DataTables\Curriculum;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubjectDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Subject> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', fn(Subject $subject) => $subject->name)
            ->addColumn('code', fn(Subject $subject) => $subject->code)
            ->addColumn('description', fn(Subject $subject) => $subject->description)
            ->addColumn(
                'is_active',
                fn(Subject $subject) => view('components.status-badge', ['status' => $subject->is_active])->render()
            )
            ->addColumn(
                'created_at',
                fn(Subject $subject) => $subject->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(Subject $subject) => view('components.actions', [
                    'canView'      => true,
                    'canEdit'      => true,
                    'canDelete'    => true,
                    'routeKeyName' => 'subjects.',
                    'param'        => $subject,
                ])->render()
            )
            ->rawColumns(['is_active', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Subject>
     */
    public function query(Subject $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subject-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->responsive([
                'details' => [
                    'type'   => 'inline',
                    'target' => 'td.dtr-control',
                ],
            ])
            ->buttons([
                ['extend' => 'excel', 'exportOptions' => ['columns' => ':not(:last-child)']],
                ['extend' => 'pdf',   'exportOptions' => ['columns' => ':not(:last-child)']],
            ])
            ->layout([
                'topStart'    => 'length',  // rendered here so JS can detach it
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->columnDefs([
                ['responsivePriority' => 1, 'targets' => 0],  // Name        — always visible
                ['responsivePriority' => 3, 'targets' => 1],  // Code
                ['responsivePriority' => 4, 'targets' => 2],  // Description
                ['responsivePriority' => 5, 'targets' => 3],  // Status
                ['responsivePriority' => 6, 'targets' => 4],  // Created At
                ['responsivePriority' => 2, 'targets' => 5],  // Action      — always visible
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
            Column::make('name')->title('Subject'),
            Column::make('code')->title('Code'),
            Column::make('description')->title('Description'),
            Column::make('is_active')->title('Status'),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
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
        return 'Subject_' . date('YmdHis');
    }
}
