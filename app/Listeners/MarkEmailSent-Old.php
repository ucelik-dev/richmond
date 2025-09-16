<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;

class MarkEmailSent
{

    public function handle(MessageSent $event): void
    {
        $msg     = $event->message;
        $headers = $msg->getHeaders();

        $logKey    = $headers->getHeaderBody('X-Log-Key');
        $messageId = $headers->getHeaderBody('Message-ID') ?: null;

        // (A) Best: update by our injected X-Log-Key
        if ($logKey) {
            EmailLog::where('log_key', $logKey)->latest()->first()?->update([
                'status'     => 'sent',
                'sent_at'    => now(),
                'message_id' => $messageId, // fill if available now
            ]);
            return;
        }

        // (B) Fallback by Message-ID (when available)
        if ($messageId) {
            EmailLog::where('message_id', $messageId)->latest()->first()?->update([
                'status'  => 'sent',
                'sent_at' => now(),
            ]);
            return;
        }

        // (C) Last‑ditch: fuzzy match the latest 'sending' row for same subject/to
        $to = array_keys($msg->getTo() ?? []);
        EmailLog::query()
            ->where('status', 'sending')
            ->where('subject', $msg->getSubject())
            ->when(!empty($to), fn ($q) => $q->whereJsonContains('to', $to[0]))
            ->latest()
            ->first()?->update([
                'status'  => 'sent',
                'sent_at' => now(),
            ]);
    }

}
