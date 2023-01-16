<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('days', function (Request $request) {
    if ( ! $request->user()) {
        return [];
    }

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
});

Route::post('days/exists', function (Request $request) {
    if ( ! $request->user()) {
        abort(401);
    }

    $validated = $request->validate([
        'date' => ['required', 'date_format:Y-m-d']
    ]);

    $day = $request->user()->days()
        ->where('date', $validated['date'])
        ->first();

    if ($day) {
        return [
            'exists' => true,
            'route' => route('days.create', $day)
        ];
    }

    return [
        'exists' => false
    ];
});
