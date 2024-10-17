<?php

namespace App\Services;

use Illuminate\Http\Request;

class YearMonthService
{
    public function index(Request $request, $year = null): array
    {
        if ($year === null) {
            $year = config('calendar.year');
        }

        if (intval($year) === 0) {
            abort(404);
        }

        $defaultValues = collect([
            1 => [],
            2 => [],
            3 => [],
            4 => [],
            5 => [],
            6 => [],
            7 => [],
            8 => [],
            9 => [],
            10 => [],
            11 => [],
            12 => [],
        ])->groupBy(function ($item, $key) {
            return $key;
        })->map(function ($item) {
            return collect([]);
        });

        $days = $request->user()->days()
            ->when($request->get('tag'), function ($query, $tag) {
                $query->whereRelation('tags', 'slug', '=', $tag);
            })
            ->with('category')->year($year)->get();


        $days = $days->map(function ($day) {
            return (object)[
                'backgroundColor' => $day->category->color,
                'textColor' => $day->category->font_color,
                'start' => $day->date,
                'display' => 'background',
                'url' => route('days.edit', $day)
            ];
        })->groupBy(function ($item) {
            return (int)date_create_from_format('Y-m-d', $item->start)->format('m');
        });

        $days = $days->union($defaultValues);

        return [
            'days' => $days,
            'year' => $year
        ];
    }
}
