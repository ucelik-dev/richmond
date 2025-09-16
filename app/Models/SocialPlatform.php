<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialPlatform extends Model
{
    protected $guarded = [];

    public function userAccounts()
    {
        return $this->hasMany(UserSocialPlatform::class, 'social_platform_id');
    }
}
