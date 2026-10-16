<?php

namespace App\Http\Middleware;

use App\Services\TimezoneService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the signed-in user's time zone and activity up to date, for the web
 * and for API tokens alike.
 */
class TrackUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $changes = [];

            if ($user->timezone_detect) {
                $timezone = (new TimezoneService)->detect($request, $user->timezone);
                if ($timezone && $timezone !== $user->timezone) {
                    $changes['timezone'] = $timezone;
                }
            }

            // Once a day is enough for the inactive-user cleanup.
            if ( ! $user->last_active_at || ! $user->last_active_at->isToday()) {
                $changes['last_active_at'] = now();
            }

            if ($changes) {
                // Straight to the table so updated_at keeps meaning "profile changed".
                DB::table('users')->where('id', $user->id)->update($changes);
                $user->forceFill($changes)->syncOriginal();
            }
        }

        return $next($request);
    }
}
