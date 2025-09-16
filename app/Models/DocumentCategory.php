<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    protected $guarded = [];

    public function documents()
    {
        return $this->hasMany(UserDocument::class, 'category_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
}
