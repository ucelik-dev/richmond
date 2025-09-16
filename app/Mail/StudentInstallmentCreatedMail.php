<?php

namespace App\Mail;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Mail\Mailables\Headers;
use Str;

class StudentInstallmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $installments;
    public string $logKey;

    /* Create a new message instance. */
    public function __construct(Payment $payment, Collection $installments)
    {
        $this->payment = $payment;
        $this->installments = $installments;
        $this->logKey = (string) Str::uuid();
    }

    /* Get the message envelope. */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Richmond College — Your Installments Created',
        );
    }

    /* Get the message content definition. */
    public function content(): Content
    {
        return new Content(
            view: 'mail.student-installment-create-mail',
            with: ['payment' => $this->payment, 'installments' => $this->installments],
        );
    }

    /* Get the attachments for the message. */
    public function attachments(): array
    {
        return [];
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-App-Mailable' => static::class,
            'X-User-ID'      => (string) $this->payment->user_id,
            'X-Log-Key'      => $this->logKey,
        ]);
    }

}
