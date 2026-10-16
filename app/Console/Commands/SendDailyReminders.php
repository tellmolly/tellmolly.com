<?php

namespace App\Console\Commands;

use App\Mail\DailyReminder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDailyReminders extends Command
{
    protected $signature = 'tm:send-daily-reminders';

    protected $description = 'Email users who turned on reminders and have not logged today, at their chosen time in their time zone';

    /**
     * How long after the chosen time a reminder may still go out, so a late or
     * skipped scheduler run doesn't drop it, but nobody gets one hours late.
     */
    private const WINDOW_MINUTES = 60;

    public function handle(): void
    {
        $sent = 0;

        User::query()
            ->where('reminder_enabled', '=', true)
            ->where('email', '<>', (string) config('calendar.demo.email'))
            ->each(function (User $user) use (&$sent) {
                $now = $user->localNow();
                $today = $now->format('Y-m-d');

                if ($user->reminded_on === $today) {
                    return;
                }

                $remindAt = $now->setTimeFromTimeString($user->reminder_time);
                if ($now->lessThan($remindAt) || $now->greaterThanOrEqualTo($remindAt->addMinutes(self::WINDOW_MINUTES))) {
                    return;
                }

                if ($user->days()->where('date', '=', $today)->exists()) {
                    return;
                }

                try {
                    Mail::to($user)->send(new DailyReminder($user));
                } catch (Throwable $e) {
                    report($e);

                    return;
                }

                DB::table('users')->where('id', $user->id)->update(['reminded_on' => $today]);
                $sent++;
            });

        $this->info($sent . ' reminders sent');
    }
}
