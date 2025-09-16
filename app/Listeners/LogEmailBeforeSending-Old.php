<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

class LogEmailBeforeSending
{

    public function handle(MessageSending $event): void
    {
        $msg      = $event->message;                 
        $headers  = $msg->getHeaders();             
        $all      = $headers->all();                 

        $toList  = collect($msg->getTo()  ?? [])->map(fn($a) => $a->getAddress())->values();

        // Extract the "to" addresses cleanly
        $to = collect($msg->getTo())->map(function ($address) {
            return $address->getAddress();   // just the email
            // or: return $address->toString(); // email + name
        })->implode(',');

         // Ensure there is a correlation key on the message itself
        $logKey = $headers->getHeaderBody('X-Log-Key');
        if (!$logKey) {
            $logKey = (string) Str::uuid();
            // Add header to the email so it reaches MessageSent
            $headers->addTextHeader('X-Log-Key', $logKey);
        }

        // Convert each header to a trimmed string (e.g. "Subject: ...")
        $headerLines = [];
        foreach ($all as $h) {
            // HeaderInterface::toString() returns "Name: value\r\n"
            $headerLines[] = trim($h->toString());
        }

        $userId = $headers->getHeaderBody('X-User-ID');
        if (!$userId && $toList->isNotEmpty()) {
            $userId = User::where('email', $toList->first())->value('id');
        }

        // Prevent duplicates even if event fires twice
        EmailLog::updateOrCreate(
            ['log_key' => $logKey],
            [
                'mailable'   => $headers->getHeaderBody('X-App-Mailable'),
                'user_id'    => $userId,
                'subject'    => $msg->getSubject(),
                'to'         => $to,
                'cc'         => array_keys($msg->getCc() ?? []),
                'bcc'        => array_keys($msg->getBcc() ?? []),
                'html'       => $msg->getHtmlBody(),
                'text'       => $msg->getTextBody(),
                'headers'    => $headerLines,
                'message_id' => $headers->getHeaderBody('Message-ID') ?: null,
                'status'     => 'sending',
                'sent_at'    => now(),
            ]
        );
    }

}
