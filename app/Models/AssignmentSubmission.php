<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $guarded = [];

    public function files()
    {
        return $this->hasMany(AssignmentSubmissionFile::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

}
