<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'status_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    /**
     * Get the sales person associated with the payment.
     */
    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    /**
     * Get the agent associated with the payment.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

}
