<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ( ! $request->user()) {
            return redirect()->route('login');
        }

        return view('calendar.index');
    }
}
