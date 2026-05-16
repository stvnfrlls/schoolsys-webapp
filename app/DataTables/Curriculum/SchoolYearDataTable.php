<?php

namespace App\DataTables\Curriculum;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SchoolYearDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<SchoolYear> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->addColumn('name', fn(SchoolYear $schoolyear) => $schoolyear->name)
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('school_years.name', 'ilike', "%{$keyword}%");
            })
            ->addColumn(
                'start_date',
                fn(SchoolYear $schoolyear) =>
                $schoolyear->start_date->format('M d, Y')
            )
            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereRaw("TO_CHAR(start_date, 'Mon DD, YYYY') ILIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn(
                'end_date',
                fn(SchoolYear $schoolyear) =>
                $schoolyear->end_date->format('M d, Y')
            )
            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereRaw("TO_CHAR(end_date, 'Mon DD, YYYY') ILIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn(
                'is_active',
                fn(SchoolYear $schoolyear) =>
                view('components.status-badge', ['status' => $schoolyear->is_active])->render()
            )
            ->filterColumn('is_active', function ($query, $keyword) {
                $query->where('school_years.is_active', 'ilike', "%{$keyword}%");
            })
            ->addColumn(
                'created_at',
                fn(SchoolYear $schoolyear) =>
                $schoolyear->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(SchoolYear $schoolyear) =>
                view('components.actions', [
                    'canView'      => $authUser->hasPermissionTo('view school years'),
                    'canEdit'      => $authUser->hasPermissionTo('edit school years'),
                    'canDelete'    => $authUser->hasPermissionTo('delete school years'),
                    'routeKeyName' => 'schoolyears.',
                    'param'        => $schoolyear
                ])->render()
            )
            ->rawColumns(['is_active', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<SchoolYear>
     */
    public function query(SchoolYear $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('schoolyears-table')
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
                ['responsivePriority' => 1, 'targets' => 0], // Name        — always visible
                ['responsivePriority' => 2, 'targets' => -1], // Actions    — always visible
                ['responsivePriority' => 3, 'targets' => 3], // Status
                ['responsivePriority' => 4, 'targets' => 1], // Start Date  — collapses next
                ['responsivePriority' => 5, 'targets' => 2], // End Date    — collapses next
                ['responsivePriority' => 6, 'targets' => 4], // Created At  — collapses first
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
            Column::computed('name')->title('School Year')->searchable(true),
            Column::computed('start_date')->title('Start Date')->searchable(true),
            Column::computed('end_date')->title('End Date')->searchable(true),
            Column::computed('is_active')->title('Status')->searchable(true),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->searchable(false)
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
        return 'SchoolYear_' . date('YmdHis');
    }
}
