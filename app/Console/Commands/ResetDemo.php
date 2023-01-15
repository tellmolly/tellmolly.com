<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetDemo extends Command
{
    protected $signature = 'tm:reset-demo';

    protected $description = 'Reset the demo to it\'s default state.';

    public function handle(): int
    {
        if ( ! config('calendar.actions.demo') || ! config('calendar.demo.email')) {
            return Command::SUCCESS;
        }

        $demoUser = User::query()
            ->where([
                'email' => config('calendar.demo.email')
            ])
            ->first();

        // Check last access
        // If no login in last hour, no changes (except database)
        // Prevents IDs from becoming unnecessarily large
        if (Carbon::now()->gt(Carbon::parse($demoUser->last_login_at)->addHour())) {
            $this->info('No sign-in since last reset');

            return Command::SUCCESS;
        }

        $demoUser->tags()->delete();

        $demoUser->days()->delete();

        $this->call('db:seed', [
            'class' => 'DemoSeeder',
            '--force' => true
        ]);

        return Command::SUCCESS;
    }
}
