<?php

namespace App\Console\Commands;

use App\Mail\AccountInactive;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyInactiveUsers extends Command
{
    protected $signature = 'tm:notify-inactive-users';

    protected $description = 'Notify users that have a) no entries and no activity in the last 30 days or b) no activity for the last 180 days';

    public function handle(): void
    {
        $inactiveNewUsers = User::query()
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('days')
                    ->whereColumn('users.id', 'user_id');
            })
            ->where('last_login_at', '<', now()->subDays(30)->format('Y-m-d') . ' 00:00:00')
            ->where('last_login_at', '>=', now()->subDays(31)->format('Y-m-d') . ' 00:00:00')
            ->get();

        $this->info($inactiveNewUsers->count() . ' inactive new users');

        foreach ($inactiveNewUsers as $user) {
            Mail::to($user)->send(new AccountInactive($user, 30, 180));
        }

        $inactiveExistingUsers = User::query()
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('days')
                    ->whereColumn('users.id', 'user_id');
            })
            ->where('last_login_at', '<', now()->subDays(180)->format('Y-m-d') . ' 00:00:00')
            ->where('last_login_at', '>=', now()->subDays(181)->format('Y-m-d') . ' 00:00:00')
            ->get();

        $this->info($inactiveExistingUsers->count() . ' inactive existing users');

        foreach ($inactiveExistingUsers as $user) {
            Mail::to($user)->send(new AccountInactive($user, 180, 30));
        }
    }
}
