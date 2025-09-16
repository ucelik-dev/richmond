<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwardingBody extends Model
{
    protected $guarded = [];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function registrations()
    {
        return $this->hasMany(AwardingBodyRegistration::class);
    }
    
    public function modules()
    {
        return $this->hasMany(Module::class, 'awarding_body_id');
    }
    
}
