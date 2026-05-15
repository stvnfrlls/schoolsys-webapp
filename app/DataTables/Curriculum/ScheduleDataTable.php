<?php

namespace App\DataTables\Curriculum;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

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
                fn(Schedule $schedule) => $schedule->faculty
                    ? ($schedule->faculty->gender === 'male' ? 'Mr. ' : 'Ms. ') . $schedule->faculty->last_name
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
                $schedule->subject
                    ? '<span title="' . e($schedule->subject->name) . '">'
                    . e($schedule->subject->name)
                    . '</span>'
                    : '—'
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
            ->filterColumn('faculty', function ($query, $keyword) {
                $query->whereRaw(
                    "CONCAT(
                        CASE faculties.gender WHEN 'male' THEN 'Mr. ' ELSE 'Ms. ' END,
                        faculties.last_name
                    ) ILIKE ?",
                    ["%{$keyword}%"]
                );
            })
            ->filterColumn('subject', function ($query, $keyword) {
                $query->where('subjects.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('school_year', function ($query, $keyword) {
                $query->where('school_years.name', 'ilike', "%{$keyword}%");
            })
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas(
                    'section',
                    fn($q) =>
                    $q->where('name', 'ilike', "%{$keyword}%")
                );
            })
            ->filterColumn('day_of_week', function ($query, $keyword) {
                $days = [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday',
                ];

                // Find all day numbers where the name matches the keyword
                $matchingDays = array_keys(array_filter(
                    $days,
                    fn($name) => stripos($name, $keyword) !== false
                ));

                if (!empty($matchingDays)) {
                    $query->whereIn('schedules.day_of_week', $matchingDays);
                }
            })
            ->rawColumns(['action', 'subject'])
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
            ->join('subjects', 'subjects.id', '=', 'schedules.subject_id')
            ->join('school_years', 'school_years.id', '=', 'schedules.school_year_id')
            ->select(
                'schedules.*',
                DB::raw("CONCAT(faculties.last_name, ', ', faculties.first_name) as faculty_name"),
                'subjects.name as subject_name',
                'school_years.name as school_year_name',
            );
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
            Column::computed('faculty')->title('Faculty')->searchable(true),
            Column::computed('section')->title('Section')->searchable(true),
            Column::computed('subject')->title('Subject')->searchable(true),
            Column::computed('day_of_week')->title('Day')->searchable(true),
            Column::computed('time')->title('Time')->searchable(true),
            Column::computed('school_year')->title('School Year')->searchable(true),
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
        return 'Schedule_' . date('YmdHis');
    }
}
