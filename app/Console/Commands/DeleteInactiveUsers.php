<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteInactiveUsers extends Command
{
    protected $signature = 'tm:delete-inactive-users';

    protected $description = 'Delete users that have a) no entries and no activity in the last 30+180 days or b) no activity for the last 180+30 days';

    public function handle(): void
    {
        $inactiveUsersQuery = User::query()
            ->where('last_login_at', '<', now()->subDays(30 + 180)->format('Y-m-d') . ' 00:00:00');

        $affected = $inactiveUsersQuery->count();

        if ($affected === 0) {
            $this->info('No inactive users detected');

            return;
        }

        $this->info('Deleting ' . $affected . ' inactive users');

        if ( ! $inactiveUsersQuery->delete()) {
            $this->error('Failed deleting ' . $affected . ' inactive users');

            return;
        }

        $this->info('Deleted ' . $affected . ' inactive users');
    }
}
