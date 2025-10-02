<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCouponUsage extends Model
{
    protected $guarded = [];
    protected $casts = ['used_at' => 'datetime'];

    public function coupon()  { return $this->belongsTo(DiscountCoupon::class, 'coupon_id'); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function payment() { return $this->belongsTo(Payment::class, 'payment_id'); }
}
