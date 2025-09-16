<?php

namespace App\Listeners;

use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

class LogEmailBeforeSending
{
    public function handle(MessageSending $event): void
    {
        $msg     = $event->message;      // Symfony\Component\Mime\Email
        $headers = $msg->getHeaders();   // Symfony\Component\Mime\Header\Headers

        // ---- helpers (do NOT change stored shapes) ----
        $addresses = function (?array $list): array {
            // returns ["a@x.com","b@y.com"]
            if (empty($list)) return [];
            return array_values(array_map(fn($a) => $a->getAddress(), $list));
        };

        $headerLines = function () use ($headers): array {
            // returns ["Subject: ...", "From: ...", "To: ..."]
            $out = [];
            foreach ($headers->all() as $h) {
                $out[] = trim($h->toString()); // "Name: value"
            }
            return $out;
        };

        // ---- recipients (KEEP to AS STRING) ----
        $toArray  = $addresses($msg->getTo());
        $toString = implode(',', $toArray);          // <- like your original
        $ccArray  = $addresses($msg->getCc());       // JSON column -> store array
        $bccArray = $addresses($msg->getBcc());      // JSON column -> store array

        // ---- correlation key (keep if provided; otherwise add UUID) ----
        $logKey = $headers->getHeaderBody('X-Log-Key');
        if (!$logKey) {
            $logKey = (string) Str::uuid();
            $headers->addTextHeader('X-Log-Key', $logKey);
        }

        // ---- resolve user_id (prefer header, else first TO) ----
        $userId = $headers->getHeaderBody('X-User-ID');
        if (!$userId && !empty($toArray)) {
            $userId = User::where('email', $toArray[0])->value('id');
        }

        // ---- write exactly in your shapes ----
        EmailLog::updateOrCreate(
            ['log_key' => $logKey],
            [
                'mailable'   => $headers->getHeaderBody('X-App-Mailable'),
                'user_id'    => $userId ?: null,
                'subject'    => $msg->getSubject(),
                'to'         => $toString,                 // STRING (not JSON array)
                'cc'         => $ccArray,                  // JSON array ok
                'bcc'        => $bccArray,                 // JSON array ok
                'html'       => $msg->getHtmlBody(),
                'text'       => $msg->getTextBody(),
                'headers'    => $headerLines(),            // array of "Name: value"
                'message_id' => $headers->getHeaderBody('Message-ID') ?: null,
                'status'     => 'sending',
                'sent_at'    => now(),
            ]
        );
    }
}
