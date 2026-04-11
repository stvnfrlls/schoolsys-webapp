<?php

namespace App\DataTables\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<User> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('name', fn(User $user) => $user->name)
            ->addColumn('email', fn(User $user) => $user->email)
            ->addColumn(
                'roles',
                fn(User $user) =>
                $user->roles->pluck('name')->implode(', ') ?: 'No roles'
            )
            ->addColumn(
                'status',
                fn(User $user) =>
                view('components.status-badge', ['status' => $user->status])->render()
            )
            ->addColumn(
                'created_at',
                fn(User $user) =>
                $user->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(User $user) =>
                view('components.actions', [
                    'canView' => true,
                    'canEdit' => true,
                    'canDelete' => true,
                    'routeKeyName' => 'users.',
                    'param' => $user
                ])->render()
            )
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<User>
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->with('roles');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->layout([
                'topStart' => [
                    'buttons' => [
                        'buttons' => [
                            Button::make('excel')
                                ->text('Excel')
                                ->attr(['data-icon' => 'excel']),
                            Button::make('pdf')
                                ->text('PDF')
                                ->attr(['data-icon' => 'pdf']),
                        ]
                    ]
                ],
                'topEnd' => 'search',
                'bottomStart' => 'info',
                'bottomEnd' => 'paging',
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
            Column::make('name')->title('Name'),
            Column::make('email')->title('Email'),
            Column::make('roles')->title('Roles'),
            Column::make('status')->title('Status'),
            Column::make('created_at')->title('Joined'),
            Column::computed('action')->title('Actions')->exportable(false)->printable(false)->width(100)->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
