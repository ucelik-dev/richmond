<?php

namespace App\Http\Controllers\Frontend\Student;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentEmailLogController extends Controller
{
    public function index()
    {
        $emailLogs = EmailLog::where('user_id', Auth::user()->id)->orderBy('sent_at', 'DESC')->get();
        
        return view('frontend.student.email-log.index', compact('emailLogs'));
    }

    public function show(string $id)
    {
        $emailLog = EmailLog::with('user')->findOrFail($id);
        
        return view('frontend.student.email-log.show', compact('emailLog'));
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

}
