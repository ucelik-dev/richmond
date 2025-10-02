<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCoupon extends Model
{
    protected $guarded = [];
    protected $casts = ['status' => 'boolean'];

    public function agent() { return $this->belongsTo(User::class, 'agent_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function usages() { return $this->hasMany(DiscountCouponUsage::class,'coupon_id'); }
    public function payments() { return $this->hasMany(Payment::class, 'coupon_id'); }

    public function scopeUsable($q) {
        return $q->where('status', true);
    }

    public function remainingUses(): ?int {
        if (is_null($this->max_uses)) return null;
        return max(0, $this->max_uses - $this->usages()->count());
    }

    public function computeDiscount(float $amount): float
    {
        if ($this->discount_type === 'percent') {
            $pct = min(100, max(0.01, (float)$this->discount_value));
            return round($amount * ($pct / 100), 2);
        }
        return round(max(0, (float)$this->discount_value), 2);
    }
}
