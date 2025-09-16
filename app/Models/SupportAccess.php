<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportAccess extends Model
{
    protected $guarded = [];
    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    public function targetUser() { return $this->belongsTo(User::class, 'target_user_id'); }
    public function admin() { return $this->belongsTo(User::class, 'created_by_admin_id'); }

    public function isValid(): bool {
        return is_null($this->used_at) && now()->lt($this->expires_at);
    }
}
