<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_actve'
    ];

    public function subjectPerLevel()
    {
        return $this->hasMany(SubjectPerLevel::class);
    }
}
