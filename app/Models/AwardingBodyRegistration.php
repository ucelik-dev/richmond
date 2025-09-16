<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardingBodyRegistration extends Model
{
    
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function awardingBody()
    {
        return $this->belongsTo(AwardingBody::class);
    }

    public function registrationLevel()
    {
        return $this->belongsTo(CourseLevel::class, 'awarding_body_registration_level_id');
    }

    
}
