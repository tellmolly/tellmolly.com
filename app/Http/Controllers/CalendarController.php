<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        if ( ! $request->user()) {
            return [];
        }

        $days = $request->user()->days()->with('category')->whereBetween('date', [
            $request->start,
            $request->end
        ])->get();

        return $days->map(function ($day) {
            return [
                'backgroundColor' => $day->category->color,
                'textColor' => $day->category->font_color,
                'start' => $day->date,
                'display' => 'background'
            ];
        });
    }

    public function show(): View
    {
        return view('calendar.index');
    }
}
