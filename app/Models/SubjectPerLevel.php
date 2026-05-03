<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
