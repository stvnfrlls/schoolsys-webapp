<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $gradelevel_id
 * @property int    $subject_id
 * @property int    $hours_per_week
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read GradeLevel $gradeLevel
 * @property-read Subject    $subject
 */
class SubjectPerLevel extends Model
{
    protected $fillable = [
        'gradelevel_id',
        'subject_id',
        'hours_per_week',
    ];

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'gradelevel_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
