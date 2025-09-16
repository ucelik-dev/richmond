<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_modules')
                    ->withTimestamps();
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_modules')
                    ->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function level()
    {
        return $this->belongsTo(CourseLevel::class, 'level_id');
    }

    public function awardingBody()
    {
        return $this->belongsTo(AwardingBody::class, 'awarding_body_id');
    }
    
}
