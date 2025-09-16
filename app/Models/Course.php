<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'course_modules')
                    ->withTimestamps();
    }

    public function level()
    {
        return $this->belongsTo(CourseLevel::class, 'level_id');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function awardingBody()
    {
        return $this->belongsTo(AwardingBody::class, 'awarding_body_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    
}
