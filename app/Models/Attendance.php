<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Attendance extends Model
{
    protected $fillable = [
        'schedule_id',
        'student_id',
        'date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePresent(Builder $query): Builder
    {
        return $query->where('status', 'present');
    }

    public function scopeLate(Builder $query): Builder
    {
        return $query->where('status', 'late');
    }

    public function scopeAbsent(Builder $query): Builder
    {
        return $query->where('status', 'absent');
    }

    public function scopeExcused(Builder $query): Builder
    {
        return $query->where('status', 'excused');
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForSchedule(Builder $query, int $scheduleId): Builder
    {
        return $query->where('schedule_id', $scheduleId);
    }

    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    public function isAttended(): bool
    {
        return in_array($this->status, ['present', 'late']);
    }

    /**
     * Attendance rate for a student under a set of schedule IDs.
     */
    public static function rateForStudent(int $studentId, array $scheduleIds): float
    {
        if (empty($scheduleIds)) {
            return 0.0;
        }

        $total = Attendance::forStudent($studentId)
            ->whereIn('schedule_id', $scheduleIds)
            ->count();

        if ($total === 0) {
            return 0.0;
        }

        $attended = Attendance::forStudent($studentId)
            ->whereIn('schedule_id', $scheduleIds)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return round(($attended / $total) * 100, 1);
    }
}
