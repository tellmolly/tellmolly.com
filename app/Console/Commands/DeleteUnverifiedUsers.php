<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteUnverifiedUsers extends Command
{
    public const DAYS = 7;

    protected $signature = 'tm:delete-unverified-users';

    protected $description = 'Delete accounts whose email address was not confirmed within ' . self::DAYS . ' days of signing up';

    public function handle(): void
    {
        // An unconfirmed account can't use the app, so nothing is lost; it
        // mostly frees the address for its real owner (and clears bot sign-ups).
        $query = User::query()
            ->whereNull('email_verified_at')
            ->where('created_at', '<', now()->subDays(self::DAYS));

        $count = $query->count();
        $query->delete();

        $this->info('Deleted ' . $count . ' unconfirmed ' . ($count === 1 ? 'account' : 'accounts'));
    }
}
