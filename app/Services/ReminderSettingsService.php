<?php

namespace App\Services;

use App\Http\Requests\ReminderSettingsRequest;
use App\Models\User;

class ReminderSettingsService
{
    public function update(ReminderSettingsRequest $request): User
    {
        $user = $request->user();

        if ($user->email === config('calendar.demo.email')) {
            throw new \RuntimeException('Invalid email address provided');
        }

        $validated = $request->validated();
        $detect = $validated['timezone'] === 'auto';

        $user->forceFill([
            'reminder_enabled' => $request->boolean('reminder_enabled'),
            'reminder_time' => $validated['reminder_time'] . ':00',
            'timezone_detect' => $detect,
            // When switching back to automatic, keep the last zone until a request says otherwise.
            'timezone' => $detect ? $user->timezone : $validated['timezone'],
        ])->save();

        return $user;
    }

    public function settings(User $user): array
    {
        return [
            'reminder_enabled' => $user->reminder_enabled,
            'reminder_time' => substr($user->reminder_time, 0, 5),
            'timezone' => $user->localTimezone(),
            'timezone_detect' => $user->timezone_detect,
        ];
    }
}
