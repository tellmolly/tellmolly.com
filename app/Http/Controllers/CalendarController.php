<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CalendarController extends Controller
{
    public function index(): View|RedirectResponse
    {
        return view('calendar.index');
    }
}
