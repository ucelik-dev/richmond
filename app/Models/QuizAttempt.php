<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'score', 'passed', 'submitted_at'];

    public function quiz() {
        return $this->belongsTo(Quiz::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function answers() {
        return $this->hasMany(QuizAttemptAnswer::class, 'attempt_id');
    }
}
