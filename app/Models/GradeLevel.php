<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 * @property int    $level
 * @property string $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Section>          $section
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SubjectPerLevel>  $subjectPerLevel
 */
class GradeLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'is_active',
    ];

    public function section()
    {
        return $this->hasMany(Section::class);
    }

    public function subjectPerLevel()
    {
        return $this->hasMany(SubjectPerLevel::class);
    }
}
