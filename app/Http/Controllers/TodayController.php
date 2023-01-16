<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TodayController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $today = Carbon::now()->toDateString();

        $day = $request->user()->days()
            ->where('date', $today)
            ->first();

        if ($day) {
            return redirect()->route('days.edit', $day);
        }

        return redirect()->route('days.create');
    }
}
