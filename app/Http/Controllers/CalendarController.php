<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CalendarController extends Controller
{
    public function index(): View
    {
        return view('calendar.index');
    }
}
