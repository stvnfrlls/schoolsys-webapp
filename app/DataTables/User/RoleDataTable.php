<?php

namespace App\DataTables\User;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Role> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->filterColumn('name', fn($query, $keyword) => $query->where('name', 'ilike', "%{$keyword}%"))
            ->editColumn("permission_count", fn(Role $role) => $role->permission_count ?? 0)
            ->editColumn("user_count", fn(Role $role) => $role->user_count ?? 0)
            ->editColumn("created_at", fn(Role $role) => $role->created_at->format("M d, Y"))
            ->addColumn(
                'action',
                fn(Role $role) =>
                view('components.actions', [
                    'canView'      => $authUser->hasPermissionTo('view roles'),
                    'canEdit'      => $authUser->hasPermissionTo('edit roles'),
                    'canDelete'    => $authUser->hasPermissionTo('delete roles'),
                    'routeKeyName' => 'roles.',
                    'param'        => $role
                ])->render()
            )
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Role>
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()
            ->withCount(['permissions', 'users']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('roles-table')
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
                ['responsivePriority' => 3, 'targets' => 0],
                ['responsivePriority' => 4, 'targets' => 2],
                ['responsivePriority' => 5, 'targets' => 3],
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
            Column::make('id')->title('#')->width(50)->searchable(false),
            Column::make('name')->title('Role Name'),
            Column::make('permissions_count')->title('Permissions')->searchable(false)->orderable(true),
            Column::make('users_count')->title('Users')->searchable(false)->orderable(true),
            Column::make('created_at')->title('Created')->searchable(false),
            Column::computed('action')->title('Actions')->exportable(false)->printable(false)->searchable(false)->orderable(false),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Role_' . date('YmdHis');
    }
}
