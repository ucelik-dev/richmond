<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(RecruitmentStatus::class, 'status_id');
    }

    public function source()
    {
        return $this->belongsTo(RecruitmentSource::class, 'source_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function callLogs()
    {
        return $this->hasMany(RecruitmentCall::class);
    }

}
