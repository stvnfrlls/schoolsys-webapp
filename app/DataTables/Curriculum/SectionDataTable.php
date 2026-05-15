<?php

namespace App\DataTables\Curriculum;

use App\Models\Section;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SectionDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Section> $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('grade_level', fn(Section $section) => $section->gradeLevel->name)
            ->addColumn('name', fn(Section $section) => $section->name)
            ->addColumn(
                'is_active',
                fn(Section $section) => view('components.status-badge', ['status' => $section->is_active])->render()
            )
            ->addColumn(
                'created_at',
                fn(Section $section) => $section->created_at->format('M d, Y')
            )
            ->addColumn(
                'action',
                fn(Section $section) =>
                view('components.actions', [
                    'canView' => true,
                    'canEdit' => true,
                    'canDelete' => true,
                    'routeKeyName' => 'sections.',
                    'param' => $section
                ])->render()
            )
            ->filterColumn('grade_level', function ($query, $keyword) {
                $query->where('grade_levels.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('sections.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('sections.status', 'ilike', "%{$keyword}%");
            })
            ->rawColumns(['is_active', 'action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Section>
     */
    public function query(Section $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['gradeLevel'])                                          // relation is camelCase
            ->join('grade_levels', 'grade_levels.id', '=', 'sections.grade_level_id') // ← correct table names
            ->select('sections.*');                                         // ← was 'subject.*'
    }
    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('section-table')
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
                ['responsivePriority' => 1, 'targets' => 1],  // Name  — always visible
                ['responsivePriority' => 2, 'targets' => -1], // Actions — always visible
                ['responsivePriority' => 3, 'targets' => 2],  // Status
                ['responsivePriority' => 4, 'targets' => 3],  // Created At — collapses first
                ['responsivePriority' => 5, 'targets' => 0],  // Grade Level  — always visible
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
            Column::computed('grade_level')->title('Grade Level')->searchable(true),
            Column::computed('name')->title('Section Name')->searchable(true),
            Column::computed('is_active')->title('Status')->searchable(true),
            Column::make('created_at')->title('Created At'),
            Column::computed('action')
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
        return 'Section_' . date('YmdHis');
    }
}
