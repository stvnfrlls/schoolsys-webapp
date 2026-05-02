<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'grade_level_id',
        'name',
        'is_active'
    ];

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }
}
