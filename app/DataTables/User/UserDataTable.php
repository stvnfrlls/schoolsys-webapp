<?php

namespace App\DataTables\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
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
                fn(User $user) => $user->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(User $user) =>
                view('components.actions', [
                    'canView'      => true,
                    'canEdit'      => true,
                    'canDelete'    => true,
                    'routeKeyName' => 'users.',
                    'param'        => $user
                ])->render()
            )
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('users.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('users.email', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('roles', function ($query, $keyword) {
                $query->whereHas(
                    'roles',
                    fn($q) =>
                    $q->where('name', 'ilike', "%{$keyword}%")
                );
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('users.status', 'ilike', "%{$keyword}%");
            })
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
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $baseQuery = $model->newQuery()->with('roles');

        // ── Admin: see all users ──────────────────────────────────
        if ($user->hasRole('Admin')) {
            return $baseQuery;
        }

        // ── Faculty / Student: see only their own account ─────────
        return $baseQuery->where('id', $user->id);
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
                ['responsivePriority' => 1, 'targets' => 0], // Name   — always visible
                ['responsivePriority' => 2, 'targets' => -1], // Actions — always visible
                ['responsivePriority' => 3, 'targets' => 3], // Status
                ['responsivePriority' => 4, 'targets' => 1], // Email
                ['responsivePriority' => 5, 'targets' => 2], // Roles
                ['responsivePriority' => 6, 'targets' => 4], // Joined — collapses first
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
            Column::computed('name')->title('Name')->searchable(true),
            Column::computed('email')->title('Email')->searchable(true),
            Column::computed('roles')->title('Roles')->searchable(true)->orderable(false),
            Column::computed('status')->title('Status')->searchable(true),
            Column::computed('created_at')->title('Joined')->searchable(false),
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
        return 'User_' . date('YmdHis');
    }
}
