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
});
