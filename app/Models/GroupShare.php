<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupShare extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship: belongs to a group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Relationship: belongs to an instructor (user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
