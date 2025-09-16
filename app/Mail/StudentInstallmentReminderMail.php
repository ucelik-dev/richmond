<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Support\Collection;

class StudentInstallmentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user,               // App\Models\User
        public $installment,        // App\Models\Installment
        public string $stage,       // 'due_2_days_before' | 'late_1_day_after' | 'late_1_month_after'
        public string $logKey,       // "installment:{id}:{stage}"
        public Collection|array $outstanding = []
    )
    {}

    public function envelope(): Envelope
    {
        $subjects = [
            'due_2_days_before'  => 'Upcoming Installment Due',
            'late_1_day_after'   => 'Payment Overdue: 1 Day',
            'late_1_month_after' => 'Payment Overdue: 1 Month',
        ];
        return new Envelope(subject: 'Richmond College — '.$subjects[$this->stage]);
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.student-installment-reminder-mail',
            with: [
                'user'        => $this->user,
                'installment' => $this->installment,
                'stage'       => $this->stage,
                'outstanding' => collect($this->outstanding),
            ],
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-App-Mailable' => static::class,
            'X-User-ID'      => (string) $this->user->id,
            'X-Stage'        => $this->stage,
            'X-Log-Key'      => $this->logKey,                 // <- used by listeners
            'X-Related'      => 'installment:'.$this->installment->id,
        ]);
    }

    public function attachments(): array
    {
        return [];
    }
}
