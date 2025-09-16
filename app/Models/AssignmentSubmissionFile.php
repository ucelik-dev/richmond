<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmissionFile extends Model
{
    protected $guarded = [];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }
}
