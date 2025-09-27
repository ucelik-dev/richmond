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
            if (empty($list)) return [];
            return array_values(array_map(fn($a) => $a->getAddress(), $list));
        };

        $headerLines = function () use ($headers): array {
            $out = [];
            foreach ($headers->all() as $h) {
                $out[] = trim($h->toString()); // "Name: value"
            }
            return $out;
        };

        // ---- recipients (KEEP to AS STRING) ----
        $toArray  = $addresses($msg->getTo());
        $toString = implode(',', $toArray);      // <- like your original
        $ccArray  = $addresses($msg->getCc());   // JSON column -> store array
        $bccArray = $addresses($msg->getBcc());  // JSON column -> store array

        // ---- correlation key (replace empty/duplicate; else add UUID) ----
        $logKey = (string) $headers->getHeaderBody('X-Log-Key');
        if (!$logKey) {
            $logKey = (string) Str::uuid();
            // remove any existing X-Log-Key (empty or duplicate) then add ours
            if (method_exists($headers, 'remove')) {
                $headers->remove('X-Log-Key');
            }
            $headers->addTextHeader('X-Log-Key', $logKey);
        }

        // ---- resolve user_id (prefer header, else first TO) ----
        $userId = $headers->getHeaderBody('X-User-ID');
        if (!$userId && !empty($toArray)) {
            $userId = User::where('email', $toArray[0])->value('id');
        }

        // ---- read any existing meta to preserve previous data / detect duplicates
        $existing = EmailLog::where('log_key', $logKey)->first();
        $meta = (array) optional($existing)->meta;

        // Build a set of source paths we have already copied for this log_key
        $alreadySrc = collect(data_get($meta, 'attachments', []))
            ->map(fn($a) => is_array($a) ? ($a['src'] ?? '') : '')
            ->filter()
            ->values()
            ->all();

        // ---- copy declared attachments once (idempotent)
        $copiedAttachments = [];
        if ($json = $headers->getHeaderBody('X-Attachments')) {
            $list = json_decode($json, true) ?: [];
            if (is_array($list)) {
                $dateFolder  = now()->format('Y-m-d');
                $destBaseRel = "uploads/sent-attachments/{$dateFolder}/{$logKey}";
                $destBaseAbs = public_path($destBaseRel);
                if (!is_dir($destBaseAbs)) @mkdir($destBaseAbs, 0775, true);

                foreach ($list as $a) {
                    $rel  = is_array($a) ? ($a['path'] ?? '') : (string) $a;          // source (relative to public/)
                    $name = is_array($a) ? ($a['name'] ?? basename($rel)) : basename($rel);
                    if (!$rel) continue;

                    // idempotency guard: skip if we’ve already copied this source file for this log_key
                    if (in_array($rel, $alreadySrc, true)) continue;

                    $srcAbs = public_path($rel);
                    if (!is_file($srcAbs)) continue;

                    $safe = Str::slug(pathinfo($name, PATHINFO_FILENAME)) ?: 'file';
                    $ext  = pathinfo($name, PATHINFO_EXTENSION);
                    $destFile = $safe . '_' . uniqid() . ($ext ? ".{$ext}" : '');
                    $destAbs  = $destBaseAbs . DIRECTORY_SEPARATOR . $destFile;

                    if (@copy($srcAbs, $destAbs)) {
                        $copiedAttachments[] = [
                            'name' => $name,
                            'path' => "{$destBaseRel}/{$destFile}", // relative for asset()
                            'src'  => $rel,                           // original source for idempotency
                        ];
                    }
                }
            }
        }

        if ($copiedAttachments) {
            // merge with any previously saved attachments
            $current = (array) data_get($meta, 'attachments', []);
            $meta['attachments'] = array_values(array_merge($current, $copiedAttachments));
        }

        // ---- write exactly in your shapes ----
        EmailLog::updateOrCreate(
            ['log_key' => $logKey],
            [
                'mailable'   => $headers->getHeaderBody('X-App-Mailable'),
                'user_id'    => $userId ?: null,
                'subject'    => $msg->getSubject(),
                'to'         => $toString,          // STRING (not JSON array)
                'cc'         => $ccArray,           // JSON array ok
                'bcc'        => $bccArray,          // JSON array ok
                'html'       => $msg->getHtmlBody(),
                'text'       => $msg->getTextBody(),
                'headers'    => $headerLines(),     // array of "Name: value"
                'message_id' => $headers->getHeaderBody('Message-ID') ?: null,
                'status'     => 'sending',
                'sent_at'    => now(),
                'meta'       => $meta,              // <- persist merged meta (incl. attachments)
            ]
        );
    }
}
