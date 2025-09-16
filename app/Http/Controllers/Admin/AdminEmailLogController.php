<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\EmailLogDataTable;
use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class AdminEmailLogController extends Controller
{
    public function index(EmailLogDataTable $dataTable)
    {
        $emailLogs = EmailLog::with('user')->orderBy('sent_at', 'DESC')->get();
        return $dataTable->render('admin.email-log.index', compact('emailLogs'));
    }

    public function show(string $id)
    {
        $emailLog = EmailLog::with('user')->findOrFail($id);
        
        return view('admin.email-log.show', compact('emailLog'));
    }

    public function inline(EmailLog $emailLog)
    {
        // If your column might be null, guard it:
        $html = $emailLog->html ?? '<!doctype html><html><body><p>No HTML content.</p></body></html>';

        // Replace any src="cid:xxxxx" with your real logo
        $html = preg_replace_callback(
            '/src=(["\'])\s*cid:[^"\']+\s*\1/i',
            function ($m) {
                // Replace with your public logo URL
                return 'src="' . asset('mail/logo-email.png') . '"';
            },
            $html
        );

        // Ensure all <a> open in a new tab (needed for payment links)
        // If a link already has target=..., replace it; otherwise add it.
        $html = preg_replace('/<a\b([^>]*)\btarget=(["\']).*?\2/i', '<a$1 target="_blank"', $html); // normalize existing
        $html = preg_replace('/<a\b(?![^>]*\btarget=)/i', '<a target="_blank" rel="noopener noreferrer" ', $html); // add where missing

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function downloadAttachment(\App\Models\EmailLog $emailLog, int $index)
{
    // Authorize as you already do for viewing logs
    $attachments = data_get($emailLog->meta, 'attachments', []);
    if (!is_array($attachments) || !isset($attachments[$index])) {
        abort(404);
    }

    $att = $attachments[$index];
    $relPath = $att['path'] ?? null;  // e.g. 'uploads/bulk-email/2025-09-04/file_abc.pdf'
    $name    = $att['name'] ?? basename($relPath ?? 'file');

    if (!$relPath) abort(404);

    // Resolve to absolute path under public/
    $full = public_path($relPath);

    // Guardrail: ensure it's inside the allowed folder
    $allowedDir = realpath(public_path('uploads/bulk-email'));
    $real = realpath($full);
    if (!$real || !str_starts_with($real, $allowedDir)) {
        abort(403);
    }
    if (!is_file($real)) {
        abort(404);
    }

    return response()->download($real, $name);
}


}
