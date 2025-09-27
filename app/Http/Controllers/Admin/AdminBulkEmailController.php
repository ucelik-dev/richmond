<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBulkSimpleMail;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class AdminBulkEmailController extends Controller
{
    public function create()
    {
        return view('admin.bulk-email.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'    => ['required', 'string', 'max:255'],
            'content'    => ['required', 'string'],   // HTML from TinyMCE
            'emails'     => ['required', 'string'],
            'from_name'  => ['nullable', 'string', 'max:100'],
            'from_email' => ['nullable', 'email'],
            'attachments'   => ['nullable','array'],
            'attachments.*' => ['nullable','file','max:20480'], // 20MB each
        ]);

        $files = collect($request->file('attachments', []))
            ->filter(fn ($f) => $f instanceof UploadedFile);

        // (optional) total size cap (~25MB)
        $maxTotal = 25 * 1024 * 1024;
        if ($files->sum(fn ($f) => $f->getSize()) > $maxTotal) {
            return back()->withErrors(['attachments' => 'Total attachment size must not exceed 25 MB.'])
                        ->withInput();
        }

        /** Store attachments under public/uploads/bulk-email/YYYY-MM-DD */
        
        $storedAttachments = [];
        if ($files->isNotEmpty()) {
            $dayFolder = 'uploads/sent-attachments/bulk/' . now()->format('Y-m-d');
            $absDir = public_path($dayFolder);
            if (!is_dir($absDir)) { @mkdir($absDir, 0775, true); }

            foreach ($files as $file) {
                $origName   = $file->getClientOriginalName();
                $ext    = $file->getClientOriginalExtension();
                $base   = pathinfo($origName, PATHINFO_FILENAME);
                $slug   = \Str::slug($base) ?: 'file';
                $filename = $slug . '_' . uniqid() . ($ext ? ".{$ext}" : '');
                $file->move($absDir, $filename);

                $storedAttachments[] = [
                    'name' => $origName,
                    'path' => $dayFolder . '/' . $filename, 
                ];
            }
        }

        // Treat content as HTML (TinyMCE)
        $subject   = $validated['subject'];
        $html      = $validated['content'];
        $fromName  = $validated['from_name']  ?? config('mail.from.name');
        $fromEmail = $validated['from_email'] ?? config('mail.from.address');

        // Parse recipients (newline/comma/semicolon)
        $items = preg_split('/[\s,;]+/', $validated['emails'], -1, PREG_SPLIT_NO_EMPTY);
        $emails = collect($items)
            ->map(fn ($e) => strtolower(trim($e)))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            return back()->withErrors(['emails' => 'No valid email addresses found.'])->withInput();
        }

        // Safety cap (optional)
        if ($emails->count() > 500) {
            return back()->withErrors(['emails' => 'Please limit to 500 recipients per send.'])->withInput();
        }

        // Base key to avoid double-submits
        $baseKey = 'bulk:' . now()->format('YmdHis') . ':' . Str::random(6);


        $queued = 0;
        foreach ($emails as $toEmail) {
            $logKey = $baseKey . ':to:' . sha1($toEmail);

            if (EmailLog::where('log_key', $logKey)->exists()) {
                continue; // already queued/sent
            }

            // Reserve a log row; listeners will update to "sent"
            EmailLog::create([
                'user_id'  => null,
                'mailable' => AdminBulkSimpleMail::class,
                'subject'  => $subject,
                'to'       => $toEmail, // store as string (your table/UI format)
                'meta'     => ['kind' => 'bulk', 'source' => 'manual', 'base_key' => $baseKey],
                'status'   => 'queued',
                'log_key'  => $logKey,
                'sent_at'  => now(),
                'meta'      => [
                    'kind'        => 'bulk',
                    'source'      => 'manual',
                    'attachments' => $storedAttachments, // ✅ store them here
                ],
            ]);

            // Queue one message per recipient (HTML only, with text fallback inside mailable)
            Mail::to($toEmail)->queue(
                new AdminBulkSimpleMail($subject, $html, $logKey, $fromEmail, $fromName, $storedAttachments)
            );

            $queued++;
        }

        return redirect()
            ->route('admin.bulk-email.create')
            ->with('status', "Queued {$queued} emails.");
    }
}
