<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class MarkEmailSent
{
    public function handle(MessageSent $event): void
    {
        $msg     = $event->message;                 // Symfony\Component\Mime\Email
        $headers = $msg->getHeaders();

        // the Message-ID returned by the transport (strip < > if present)
        $messageId = trim((string) $headers->getHeaderBody('Message-ID'));
        $messageId = $messageId ? trim($messageId, '<>') : null;

        // our correlation header (we add this in LogEmailBeforeSending)
        $logKey = (string) $headers->getHeaderBody('X-Log-Key');

        $data = [
            'status'     => 'sent',
            'message_id' => $messageId,
            'sent_at'    => now(),
        ];

        $updated = 0;

        if ($logKey) {
            $updated = EmailLog::where('log_key', $logKey)->update($data);
        }

        // fallback: match by Message-ID if we couldn’t match by log_key
        if (!$updated && $messageId) {
            $updated = EmailLog::whereNull('message_id')
                ->where('status', 'sending')
                ->latest('id')
                ->limit(1)
                ->update($data);
        }

        // (optional) log for troubleshooting
        if (!$updated) {
            Log::warning('MarkEmailSent did not match any row', [
                'log_key'    => $logKey,
                'message_id' => $messageId,
            ]);
        }
    }
}
