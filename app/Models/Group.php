<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];
    
    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Enrollment::class,
            'group_id',      // Foreign key on enrollments table
            'id',            // Foreign key on users table
            'id',            // Local key on groups table
            'user_id'        // Local key on enrollments table
        );
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

}
