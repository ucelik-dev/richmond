<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;

class MarkEmailSent
{

    // app/Listeners/MarkEmailSent.php
    public function handle(\Illuminate\Mail\Events\MessageSent $event): void
    {
        $headers = $event->message->getHeaders();
        $logKey  = optional($headers->get('X-Log-Key'))->getValue();
        if (!$logKey) return;

        $messageId = optional($headers->get('Message-ID'))->getValue();

        \App\Models\EmailLog::where('log_key', $logKey)->update([
            'message_id' => $messageId,
            'status'     => 'sent',
            'sent_at'    => now(),
        ]);
    }


}
