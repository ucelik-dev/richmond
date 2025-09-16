<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLevel extends Model
{
    protected $guarded = [];
    
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'level_id');
    }
}
