<?php

namespace App\DataTables\Curriculum;

use App\Models\GradeLevel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class GradeLevelDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<GradeLevel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();

        return (new EloquentDataTable($query))
            ->addColumn('name', fn(GradeLevel $gradeLevel) => $gradeLevel->name)
            ->addColumn(
                'is_active',
                fn(GradeLevel $gradeLevel) =>
                view('components.status-badge', ['status' => $gradeLevel->is_active])->render()
            )
            ->addColumn(
                'created_at',
                fn(GradeLevel $gradeLevel) =>
                $gradeLevel->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(GradeLevel $gradeLevel) =>
                view('components.actions', [
                    'canView'      => $authUser->hasPermissionTo('view grade levels'),
                    'canEdit'      => $authUser->hasPermissionTo('edit grade levels'),
                    'canDelete'    => $authUser->hasPermissionTo('delete grade levels'),
                    'routeKeyName' => 'gradelevels.',
                    'param'        => $gradeLevel
                ])->render()
            )
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('grade_levels.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('grade_levels.status', 'ilike', "%{$keyword}%");
            })
            ->rawColumns(['is_active', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<GradeLevel>
     */
    public function query(GradeLevel $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('gradelevels-table')
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
                ['responsivePriority' => 3, 'targets' => 1], // Status
                ['responsivePriority' => 4, 'targets' => 2], // Created At — collapses first
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
            Column::computed('name')->title('Grade Level')->searchable(true),
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
        return 'GradeLevel_' . date('YmdHis');
    }
}
