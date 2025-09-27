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

        // (optional) expose an attachments count for a column/badge
        foreach ($emailLogs as $log) {
            $atts = data_get($log->meta, 'attachments', []);
            if (is_string($atts)) {
                $decoded = json_decode($atts, true);
                $atts = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
            }
            $log->attachments_count = is_array($atts) ? count($atts) : 0;
        }

        return $dataTable->render('admin.email-log.index', compact('emailLogs'));
    }

    public function show(string $id)
    {
        $emailLog = EmailLog::with('user')->findOrFail($id);

        // Normalize attachments so the blade can loop easily
        $attachments = data_get($emailLog->meta, 'attachments', []);
        if (is_string($attachments)) {
            $decoded = json_decode($attachments, true);
            $attachments = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }
        $attachments = collect($attachments)->map(function ($a) {
            if (is_string($a)) {
                return [
                    'name' => basename($a),
                    'path' => $a,                      // relative to public/
                    'url'  => asset($a),
                ];
            }
            $path = $a['path'] ?? '';
            return [
                'name' => $a['name'] ?? basename($path),
                'path' => $path,
                'url'  => $path ? asset($path) : null,
            ];
        })->filter(fn ($x) => !empty($x['path']))->values();

        return view('admin.email-log.show', compact('emailLog', 'attachments'));
    }

    public function inline(EmailLog $emailLog)
    {
        $html = $emailLog->html ?? '<!doctype html><html><body><p>No HTML content.</p></body></html>';

        $html = preg_replace_callback('/src=(["\'])\s*cid:[^"\']+\s*\1/i', function () {
            return 'src="' . asset('mail/logo-email.png') . '"';
        }, $html);

        $html = preg_replace('/<a\b([^>]*)\btarget=(["\']).*?\2/i', '<a$1 target="_blank"', $html);
        $html = preg_replace('/<a\b(?![^>]*\btarget=)/i', '<a target="_blank" rel="noopener noreferrer" ', $html);

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    public function downloadAttachment(\App\Models\EmailLog $emailLog, int $index)
    {
        $attachments = data_get($emailLog->meta, 'attachments', []);
        if (is_string($attachments)) {
            $decoded = json_decode($attachments, true);
            $attachments = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }
        if (!is_array($attachments) || !isset($attachments[$index])) {
            abort(404);
        }

        $att  = $attachments[$index];
        $path = is_array($att) ? ($att['path'] ?? '') : (string) $att;
        $name = is_array($att) ? ($att['name'] ?? basename($path)) : basename($path);
        if (!$path) abort(404);

        $full = public_path($path);

        // Allow any file under public/uploads (works for bulk and sent-attachments)
        $allowedRoot = realpath(public_path('uploads'));
        $real = realpath($full);
        if (!$real || !str_starts_with($real, $allowedRoot)) abort(403);
        if (!is_file($real)) abort(404);

        return response()->download($real, $name);
    }


}
