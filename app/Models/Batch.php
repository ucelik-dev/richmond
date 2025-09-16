<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    protected $guarded = [];
    
    public function students()
    {
        return $this->hasMany(User::class, 'batch_id')->whereHas('roles', function ($query) {
            $query->where('id', 2); // Or ->where('name', 'student'); if you know the role name
        });
    }
}
