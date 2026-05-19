<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $start_date
 * @property Carbon $end_date
 */
class SchoolYear extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 'active');
    }

    public static function current(): ?self
    {
        return static::where('is_active', 'active')->first();
    }

    public function getLabelAttribute(): string
    {
        return $this->start_date->format('Y') . ' - ' . $this->end_date->format('Y');
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('start_date');
    }
}
