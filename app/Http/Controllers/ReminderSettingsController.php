<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReminderSettingsRequest;
use App\Models\User;
use App\Services\ReminderSettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ReminderSettingsController extends Controller
{
    public function update(ReminderSettingsRequest $request): RedirectResponse
    {
        try {
            (new ReminderSettingsService)->update($request);
        } catch (\RuntimeException) {
            return Redirect::route('profile.edit');
        }

        return Redirect::route('profile.edit')->with('status', 'reminders-updated');
    }

    /**
     * Landing page of the link in reminder emails. Turning reminders off takes
     * a button press, so mail scanners following the link change nothing.
     */
    public function confirmUnsubscribe(User $user): View
    {
        return view('reminders.unsubscribe', ['user' => $user, 'done' => ! $user->reminder_enabled]);
    }

    /**
     * Also the target of mail clients' one-click unsubscribe, hence no CSRF token.
     */
    public function unsubscribe(Request $request, User $user): View
    {
        $user->forceFill(['reminder_enabled' => false])->save();

        return view('reminders.unsubscribe', ['user' => $user, 'done' => true]);
    }
}
