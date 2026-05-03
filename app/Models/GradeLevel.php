<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
