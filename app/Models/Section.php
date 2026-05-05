<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property int    $grade_level_id
 * @property string $name
 * @property string $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read GradeLevel $gradeLevel
 */
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
