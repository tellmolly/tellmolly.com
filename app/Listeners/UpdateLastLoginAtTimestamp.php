<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;

class UpdateLastLoginAtTimestamp
{
    public function handle(Registered|Login $event): void
    {
        if ( ! $event->user instanceof User) {
            return;
        }

        $event->user->last_login_at = now();
        $event->user->update();
    }
}
