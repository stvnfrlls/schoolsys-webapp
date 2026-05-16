<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $student_number
 * @property string $status
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $birth_date
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $contact_number
 * @property string|null $guardian_name
 * @property string|null $guardian_contact
 * @property string|null $guardian_relationship
 * @property-read string $full_name
 */

class Student extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'student_number',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'gender',
        'address',
        'contact_number',
        'guardian_name',
        'guardian_contact',
        'guardian_relationship',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class, 'student_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function currentEnrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class)
            ->whereHas('schoolYear', fn($q) => $q->where('is_active', 'active'));
    }
}
