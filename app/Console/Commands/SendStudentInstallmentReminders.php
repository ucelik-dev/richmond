<?php

namespace App\Console\Commands;

use App\Mail\StudentInstallmentReminderMail;
use App\Models\EmailLog;
use App\Models\Installment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendStudentInstallmentReminders extends Command
{

    protected $signature = 'app:send-student-installment-reminders';

    protected $description = 'Send T-2 / T+1 / T+30 installment emails';

    public function handle(): int
    {
        $today = now()->startOfDay();

        $stages = [
            ['offset' =>  2,  'stage' => 'due_2_days_before',  'require_unpaid' => true],
            ['offset' => -1,  'stage' => 'late_1_day_after',   'require_unpaid' => true],
            ['offset' => -30, 'stage' => 'late_1_month_after', 'require_unpaid' => true],
        ];

        foreach ($stages as $s) {
            $targetDate = $today->copy()->addDays($s['offset'])->toDateString();

            $q = Installment::query()
                ->whereDate('due_date', $targetDate)
                ->with(['payment.user', 'payment.installments' => function ($q) {
                    $q->select('id','payment_id','due_date','amount','status'); // lightweight
                }]);

            if ($s['require_unpaid']) {
                $q->where('status', '!=', 'paid');
            }

            foreach ($q->get() as $i) {
                // student comes from payment->user
                $student = optional($i->payment)->user;
                if (!$student || !$student->email) {
                    continue;
                }

                // for overdue stages, skip if installment is now paid
                if (in_array($s['stage'], ['late_1_day_after','late_1_month_after']) && $i->status === 'paid') {
                    continue;
                }

                $baseKey = "installment:{$i->id}:{$s['stage']}";
                $toEmail = $student->email;
                $logKey  = $baseKey . ':to:' . sha1(strtolower($toEmail));

                if (EmailLog::where('log_key', $logKey)->exists()) {
                    continue; // already queued/sent
                }

                EmailLog::create([
                    'user_id' => $student->id, // << from payment->user
                    'mailable'=> \App\Mail\StudentInstallmentReminderMail::class,
                    'subject' => null,
                    'to'      => $toEmail, // string (matches your table/UI)
                    'meta'    => ['installment_id' => $i->id, 'stage' => $s['stage']],
                    'status'  => 'queued',
                    'log_key' => $logKey,
                    'sent_at' => now(),
                ]);

                $todayStr = $today->toDateString();

                // all earlier unpaid installments for this same payment (previous months/days)
                $outstanding = $i->payment->installments
                    ->filter(fn($row) => $row->status !== 'paid' && $row->due_date < $todayStr)
                    ->sortBy('due_date')
                    ->values();

                Mail::to($toEmail)->queue(
                    new StudentInstallmentReminderMail($student, $i, $s['stage'], $logKey, $outstanding)
                );
            }
        }

        $this->info('Installment reminders queued.');
        return self::SUCCESS;
    }

}
