<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;   // <-- required
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Str;

class AdminBulkSimpleMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private string $bulkSubject;
    private string $bodyHtml;
    private string $bodyText;
    private string $logKey;
    private string $fromEmail;
    private string $fromName;

    /** @var array<int, array{path:string,name:string,mime:?string}> */
    private array $attachmentsInfo;

    public function __construct(
        string $subject,
        string $html,
        string $logKey,
        string $fromEmail,
        string $fromName,
        array  $attachmentsInfo = []          // <-- must match controller
    ) {
        $this->bulkSubject     = $subject;
        $this->bodyHtml        = $html;
        $this->bodyText        = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        $this->logKey          = $logKey;
        $this->fromEmail       = $fromEmail;
        $this->fromName        = $fromName;
        $this->attachmentsInfo = $attachmentsInfo; // <-- store it
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->bulkSubject,
            from: new Address($this->fromEmail, $this->fromName),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bulk.html',
            text: 'mail.bulk.text',
            with: ['body' => $this->bodyHtml, 'bodyText' => $this->bodyText],
        );
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'X-App-Mailable' => static::class,
            'X-Log-Key'      => $this->logKey,
        ]);
    }

    public function attachments(): array
    {
        return collect($this->attachmentsInfo ?? [])
        ->map(function ($a) {
            // Support both shapes:
            // ['path' => 'uploads/.../file.pdf', 'name' => 'Original.pdf', 'mime' => 'application/pdf']
            // or just a string 'uploads/.../file.pdf'
            $path = is_array($a) ? ($a['path'] ?? '') : (string)$a;
            $name = is_array($a) ? ($a['name'] ?? basename($path)) : basename($path);
            $mime = is_array($a) ? ($a['mime'] ?? null) : null;

            if (!$path) {
                return null;
            }

            // Convert relative 'uploads/...' to absolute filesystem path under public/
            $full = $path;
            if (!Str::startsWith($path, ['/','\\'])    // unix or windows absolute
                && !preg_match('/^[A-Za-z]:[\\\\\\/]/', $path)) { // "C:\..."" style
                $full = public_path($path);
            }

            if (!is_file($full)) {
                // File is missing -> skip this attachment
                return null;
            }

            $att = Attachment::fromPath($full)->as($name);
            if ($mime) {
                $att = $att->withMime($mime);
            }
            return $att;
        })
        ->filter()     // remove nulls (missing files)
        ->values()
        ->all();
    }

}
