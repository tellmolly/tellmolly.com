<?php

namespace App\Console\Commands;

use App\Models\User;
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

        $demoUser->tags()->delete();

        $demoUser->days()->delete();

        $this->call('db:seed', [
            'class' => 'DemoSeeder'
        ]);

        return Command::SUCCESS;
    }
}
