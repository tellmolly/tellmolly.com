<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReminderSettingsRequest;
use App\Services\ReminderSettingsService;
use Illuminate\Http\Request;

class ReminderSettingsController extends Controller
{
    public function show(Request $request): array
    {
        return (new ReminderSettingsService)->settings($request->user());
    }

    public function update(ReminderSettingsRequest $request): array
    {
        $service = new ReminderSettingsService;

        try {
            $service->update($request);
        } catch (\RuntimeException) {
            abort(403);
        }

        return $service->settings($request->user());
    }
}
