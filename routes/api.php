<?php

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
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
            'route' => route('days.edit', $day)
        ];
    }

    return [
        'exists' => false
    ];
});

Route::post('tags', function (Request $request) {
    if ( ! $request->user()) {
        abort(401);
    }

    $validated = $request->validate([
        'name' => ['required', 'string']
    ]);

    $tag = new Tag();
    $tag->fill($validated);
    $tag->color = "#" . substr(dechex(crc32($validated['name'])), 0, 6);

    $tag =  $request->user()->tags()->save($tag);

    return [
        'name' => $tag->name,
        'slug' => $tag->slug
    ];
});
