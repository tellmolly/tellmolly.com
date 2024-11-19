<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuickDayController extends Controller
{
    public function index(Request $request)
    {
        $days = $request->user()->days()->with('category', 'tags')->whereBetween('date', [
            $request->start,
            $request->end
        ])->get();

        return $days->flatMap(function ($day) {
            $events = [
                [
                    'backgroundColor' => $day->category->color,
                    'textColor' => $day->category->font_color,
                    'start' => $day->date,
                    'display' => 'background',
                    'url' => route('days.edit', $day)
                ]
            ];
            foreach ($day->tags as $tag) {
                $events[] = [
                    'backgroundColor' => $tag->color,
                    'borderColor' => $tag->color,
                    'title' => $tag->name,
                    'start' => $day->date,
                    'url' => route('days.edit', $day)
                ];
            }

            return $events;
        });
    }

    public function exists(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d']
        ]);

        $day = $request->user()->days()
            ->where('date', $validated['date'])
            ->first();

        if ($day) {
            return [
                'exists' => true,
                'route' => route('days.edit', $day)
            ];
        }

        return [
            'exists' => false
        ];
    }
}
