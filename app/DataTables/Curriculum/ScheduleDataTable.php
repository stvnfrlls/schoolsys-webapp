<?php

namespace App\DataTables\Curriculum;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ScheduleDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder<Schedule> $query
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn(
                'faculty',
                fn(Schedule $schedule) =>
                $schedule->faculty
                    ? "{$schedule->faculty->last_name}, {$schedule->faculty->first_name}"
                    : '—'
            )
            ->addColumn(
                'section',
                fn(Schedule $schedule) =>
                $schedule->section
                    ? "{$schedule->section->gradeLevel->name} — {$schedule->section->name}"
                    : '—'
            )
            ->addColumn(
                'subject',
                fn(Schedule $schedule) =>
                $schedule->subject?->name ?? '—'
            )
            ->addColumn(
                'school_year',
                fn(Schedule $schedule) =>
                $schedule->schoolYear?->name ?? '—'
            )
            ->addColumn(
                'day_of_week',
                fn(Schedule $schedule) => $schedule->day_name
            )
            ->addColumn(
                'time',
                fn(Schedule $schedule) =>
                date('h:i A', strtotime($schedule->time_start))
                    . ' – '
                    . date('h:i A', strtotime($schedule->time_end))
            )
            ->addColumn(
                'action',
                fn(Schedule $schedule) =>
                view('components.actions', [
                    'canView'      => true,
                    'canEdit'      => true,
                    'canDelete'    => true,
                    'routeKeyName' => 'schedules.',
                    'param'        => $schedule,
                ])->render()
            )
            ->rawColumns(['action'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     *
     * @return QueryBuilder<Schedule>
     */
    public function query(Schedule $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['faculty', 'section.gradeLevel', 'subject', 'schoolYear'])
            ->join('faculties', 'faculties.id', '=', 'schedules.faculty_id')
            ->join('sections', 'sections.id', '=', 'schedules.section_id')
            ->select('schedules.*');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('schedules-table')
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
                ['responsivePriority' => 1, 'targets' => 0], // Faculty     — always visible
                ['responsivePriority' => 2, 'targets' => -1], // Actions    — always visible
                ['responsivePriority' => 3, 'targets' => 2], // Subject
                ['responsivePriority' => 4, 'targets' => 3], // Day
                ['responsivePriority' => 5, 'targets' => 4], // Time
                ['responsivePriority' => 6, 'targets' => 1], // Section
                ['responsivePriority' => 7, 'targets' => 5], // School Year — collapses first
            ])
            ->layout([
                'topStart'    => null,
                'topEnd'      => null,
                'bottomStart' => 'info',
                'bottomEnd'   => 'paging',
            ])
            ->orderBy(3, 'asc') // default: sort by day_of_week
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
            Column::computed('faculty')->title('Faculty'),
            Column::computed('section')->title('Section'),
            Column::computed('subject')->title('Subject'),
            Column::computed('day_of_week')->title('Day'),
            Column::computed('time')->title('Time'),
            Column::computed('school_year')->title('School Year'),
            Column::computed('action')
                ->title('Actions')
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
        return 'Schedule_' . date('YmdHis');
    }
}
