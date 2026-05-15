<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $student_id
 * @property int $section_id
 * @property int $school_year_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $enrolled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'section_id',
        'school_year_id',
        'status',
        'enrolled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
