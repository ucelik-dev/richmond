<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocialPlatform extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(SocialPlatform::class, 'social_platform_id');
    }
}
