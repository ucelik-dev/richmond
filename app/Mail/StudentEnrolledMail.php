<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Headers;  

class StudentEnrolledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;

    /* Create a new message instance. */
    public function __construct(User $student)
    {
        $this->student = $student;
    }

    /* Get the message envelope. */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Richmond College — Your Course Enrollment Is Complete',
        );
    }

    /* Get the message content definition. */
    public function content(): Content
    {
        return new Content(
            view: 'mail.student-enroll-mail',
            with: ['user' => $this->student],
        );
    }

    /* Get the attachments for the message. */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(public_path('mail/student-enrollment-info.pdf')),
        ];
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
