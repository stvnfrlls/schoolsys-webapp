<?php

namespace App\DataTables\User;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ActivityLogDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn(
                'causer',
                fn(Activity $activity) => $activity->causer->name ?? 'System'
            )
            ->addColumn(
                'description',
                function (Activity $activity) {
                    $color = match (true) {
                        str_contains($activity->description, 'created') => 'bg-emerald-100 text-emerald-700',
                        str_contains($activity->description, 'updated') => 'bg-blue-100 text-blue-700',
                        str_contains($activity->description, 'deleted') => 'bg-red-100 text-red-700',
                        default                                          => 'bg-gray-100 text-gray-700',
                    };

                    return "<span class='px-2 py-1 text-xs rounded-md {$color}'>{$activity->description}</span>";
                }
            )
            ->addColumn(
                'subject',
                fn(Activity $activity) =>
                class_basename($activity->subject_type) . ' #' . $activity->subject_id
            )
            ->addColumn(
                'created_at',
                fn(Activity $activity) => $activity->created_at->format('M d, Y H:i')
            )
            ->addColumn(
                'details',
                function (Activity $activity) {
                    $data    = [
                        'attributes' => $activity->properties['attributes'] ?? null,
                        'old'        => $activity->properties['old'] ?? null,
                    ];
                    $json    = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
                    $causer  = htmlspecialchars($activity->causer->name ?? 'System', ENT_QUOTES, 'UTF-8');
                    $subject = htmlspecialchars(
                        class_basename($activity->subject_type) . ' #' . $activity->subject_id,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                    $date    = $activity->created_at->format('M d, Y · H:i');

                    return '<button
                                data-log="' . $json . '"
                                data-meta="' . $causer . ' · ' . $subject . ' · ' . $date . '"
                                onclick="showDetails(this)"
                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                View
                            </button>';
                }
            )
            ->rawColumns(['description', 'details'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Activity $model): QueryBuilder
    {
        return $model->newQuery()
            ->with('causer')
            ->select('activity_log.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('activitylogs-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->responsive([
                'details' => [
                    'type'   => 'inline',
                    'target' => 'tr',
                ],
            ])
            ->setTableAttribute('dom', 'lrtip')
            ->parameters([
                'lengthChange' => true,
            ])
            ->columnDefs([
                ['responsivePriority' => 1, 'targets' => 1],  // Action  — always visible
                ['responsivePriority' => 2, 'targets' => -1], // Details — always visible
                ['responsivePriority' => 3, 'targets' => 3],  // Subject
                ['responsivePriority' => 4, 'targets' => 4],  // Date
                ['responsivePriority' => 5, 'targets' => 2],  // User
                ['responsivePriority' => 6, 'targets' => 0],  // ID      — collapses first
            ])
            ->layout([
                'topStart'    => null,
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->orderBy(0, 'desc')
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
            Column::make('id')->title('#'),
            Column::make('description')->title('Action'),
            Column::make('causer')->title('User'),
            Column::make('subject')->title('Subject'),
            Column::make('created_at')->title('Date'),
            Column::computed('details')
                ->title('Details')
                ->exportable(false)
                ->printable(false)
                ->width(80)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ActivityLog_' . date('YmdHis');
    }
}
