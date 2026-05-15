<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;

/**
 * @property string $day_name
 * @property string $day_short
 * @property int    $day_order
 * @property string $time_start
 * @property string $time_end
 */
class Schedule extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'school_year_id',
        'section_id',
        'subject_id',
        'faculty_id',
        'day_of_week',
        'time_start',
        'time_end',
        'room',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'time_start'  => 'string',
        'time_end'    => 'string',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function schoolYear(): EloquentBelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function section(): EloquentBelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): EloquentBelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function faculty(): EloquentBelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    // ─── Accessors ────────────────────────────────────────────────

    public function getDayNameAttribute(): string
    {
        return match ($this->day_of_week) {
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
            default => 'Unknown',
        };
    }

    public function getDayShortAttribute(): string
    {
        return match ($this->day_of_week) {
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            7 => 'Sun',
            default => '???',
        };
    }

    public function getDayOrderAttribute(): int
    {
        return match ($this->day_of_week) {
            'monday'    => 1,
            'tuesday'   => 2,
            'wednesday' => 3,
            'thursday'  => 4,
            'friday'    => 5,
            'saturday'  => 6,
            default     => 7,
        };
    }

    // ─── Helpers ──────────────────────────────────────────────────

    public static function hasConflict(
        int $dayOfWeek,
        string $timeStart,
        string $timeEnd,
        int $schoolYearId,
        ?int $facultyId = null,
        ?int $sectionId = null,
        ?string $room = null,
        ?int $excludeId = null
    ): bool {
        $timeStart = strlen($timeStart) === 5 ? "{$timeStart}:00" : $timeStart;
        $timeEnd   = strlen($timeEnd) === 5   ? "{$timeEnd}:00"   : $timeEnd;

        $query = static::query()
            ->where('school_year_id', $schoolYearId)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_start', '<', $timeEnd)
            ->where('time_end', '>', $timeStart)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId));

        return $query->where(function ($q) use ($facultyId, $sectionId, $room) {
            $q->when($facultyId, fn($q) => $q->orWhere('faculty_id', $facultyId))
                ->when($sectionId, fn($q) => $q->orWhere('section_id', $sectionId))
                ->when($room,      fn($q) => $q->orWhere('room', $room));
        })->exists();
    }

    protected function timeStart(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strlen($value) === 5 ? $value . ':00' : $value,
        );
    }

    protected function timeEnd(): Attribute
    {
        return Attribute::make(
            set: fn(string $value) => strlen($value) === 5 ? $value . ':00' : $value,
        );
    }

    // ─── Scopes ───────────────────────────────────────────────────

    public function scopeForSchoolYear(Builder $query, int $schoolYearId): Builder
    {
        return $query->where('school_year_id', $schoolYearId);
    }

    public function scopeForSection(Builder $query, int $sectionId): Builder
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeForTeacher(Builder $query, int $facultyId): Builder
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeForDay(Builder $query, int $dayOfWeek): Builder
    {
        return $query->where('day_of_week', $dayOfWeek);
    }
}
