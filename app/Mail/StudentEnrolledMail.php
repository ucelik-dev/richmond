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

    /* Create a new message instance. */
    public function __construct(public User $student, public ?string $logKey = null) {}

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

    public function headers(): Headers
    {
        $srcRel = 'mail/student-agreement.pdf';
        $meta = [['name' => 'Student Agreement.pdf', 'path' => $srcRel]];

        $text = [
            'X-App-Mailable' => static::class,
            'X-User-ID'      => (string) $this->student->id,
            'X-Attachments'  => json_encode($meta),
        ];
        if (!empty($this->logKey)) {
            $text['X-Log-Key'] = (string) $this->logKey;  // only if you have one
        }

        return new Headers(text: $text);
    }

    
    /* Get the attachments for the message. */
    public function attachments(): array
    {
        // where you actually put the PDF:
        $full = public_path('mail/student-agreement.pdf');

        return is_file($full)
        ? [Attachment::fromPath($full)->as('Student Agreement.pdf')->withMime('application/pdf')]
        : [];

    }

}
