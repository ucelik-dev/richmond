<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruitmentCall extends Model
{
    protected $guarded = [];
    
    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function status()
    {
        return $this->belongsTo(RecruitmentStatus::class, 'status_id');
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'called_by');
    }

}
