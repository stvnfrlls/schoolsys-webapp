<?php

namespace App\DataTables\User;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PermissionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Permission> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->editColumn(
                'created_at',
                fn(Permission $permission) => $permission->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(Permission $permission) =>
                view('components.actions', [
                    'canView' => $authUser->hasPermissionTo('view permissions'),
                    'canEdit' => $authUser->hasPermissionTo('edit permissions'),
                    'canDelete' => $authUser->hasPermissionTo('delete permissions'),
                    'routeKeyName' => 'permissions.',
                    'param' => $permission
                ])->render()
            )
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Permission>
     */
    public function query(Permission $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('permissions-table')
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
                ['responsivePriority' => 1, 'targets' => 1],
                ['responsivePriority' => 2, 'targets' => -1],
                ['responsivePriority' => 3, 'targets' => 3],
                ['responsivePriority' => 4, 'targets' => 1],
                ['responsivePriority' => 5, 'targets' => 2],
                ['responsivePriority' => 6, 'targets' => 4],
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
            Column::make('id')->title('#')->addClass('!text-center'),
            Column::make('name')->title('Permission Name'),
            Column::make('guard_name')->title('Guard')->addClass('!text-center'),
            Column::make('created_at')->title('Created')->addClass('!text-center'),
            Column::computed('action')
                ->title('Actions')
                ->exportable(false)
                ->printable(false)
                ->addClass('!text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Permission_' . date('YmdHis');
    }
}
