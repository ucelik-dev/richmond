<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Headers;  

class StudentAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;

    public function __construct($student)
    {
        $this->student = $student;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Richmond College — Your Account Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.student-create-mail',
            with: ['user' => $this->student],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-App-Mailable' => static::class,
                'X-User-ID'      => (string) $this->student->id,
            ]
        );
    }

}
