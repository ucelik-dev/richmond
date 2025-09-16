<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

      public function category()
    {
        return $this->belongsTo(DocumentCategory::class);
    }
    
}
