<?php

namespace App\DataTables\Curriculum;

use App\Models\SubjectPerLevel;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SubjectPerLevelDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<SubjectPerLevel> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('gradelevel_name', fn(SubjectPerLevel $subjectPerLevel) => $subjectPerLevel->gradelevel_name)
            ->addColumn('subject_name', fn(SubjectPerLevel $subjectPerLevel) => $subjectPerLevel->subject_name)

            ->filterColumn('gradelevel_name', function ($query, $keyword) {
                $query->whereRaw("LOWER(grade_levels.name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })

            ->filterColumn('subject_name', function ($query, $keyword) {
                $query->whereRaw("LOWER(subjects.name) LIKE ?", ["%" . strtolower($keyword) . "%"]);
            })

            ->addColumn(
                'is_active',
                fn(SubjectPerLevel $subjectPerLevel) => view('components.status-badge', [
                    'status' => $subjectPerLevel->is_active,
                ])->render()
            )

            ->addColumn(
                'action',
                fn(SubjectPerLevel $subjectPerLevel) => view('components.actions', [
                    'canView'      => true,
                    'canEdit'      => true,
                    'canDelete'    => true,
                    'routeKeyName' => 'subjectperlevel.',
                    'param'        => $subjectPerLevel,
                ])->render()
            )

            ->orderColumn('gradelevel_name', function ($query, $order) {
                $query->orderByRaw("
                    CAST(SUBSTRING_INDEX(grade_levels.name, ' ', -1) AS UNSIGNED) $order
                ");
            })

            ->rawColumns(['is_active', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<SubjectPerLevel>
     */
    public function query(SubjectPerLevel $model): QueryBuilder
    {
        $query = $model->newQuery();

        $query->join('grade_levels', 'grade_levels.id', '=', 'subject_per_levels.gradelevel_id')
            ->join('subjects', 'subjects.id', '=', 'subject_per_levels.subject_id')
            ->select([
                'subject_per_levels.*',
                'grade_levels.name as gradelevel_name',
                'subjects.name as subject_name',
            ]);

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subjectperlevel-table')
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
                'topStart'    => 'length',
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->columnDefs([
                ['responsivePriority' => 1, 'targets' => 0],  // Grade Level  — always visible
                ['responsivePriority' => 3, 'targets' => 1],  // Subject
                ['responsivePriority' => 4, 'targets' => 2],  // Hours Per Week
                ['responsivePriority' => 5, 'targets' => 3],  // Status
                ['responsivePriority' => 2, 'targets' => 4],  // Action       — always visible
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
            Column::make('gradelevel_name')->title('Grade Level'),
            Column::make('subject_name')->title('Subject'),
            Column::make('hours_per_week')->title('Hours Per Week'),
            Column::make('is_active')->title('Status'),
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
        return 'SubjectPerLevel_' . date('YmdHis');
    }
}
