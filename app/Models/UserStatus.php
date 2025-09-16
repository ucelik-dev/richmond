<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'user_status_id');
    }

}
