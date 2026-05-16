<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'faculties';

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'gender',
        'address',
        'contact_number',
        'department',
        'position',
        'rank',
        'specialization',
        'employment_type',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }
}
