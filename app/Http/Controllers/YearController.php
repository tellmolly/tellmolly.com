<?php

namespace App\Http\Controllers;

use App\Category;
use App\Day;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function index(Request $request, $year = null): View
    {
        if ($year === null) {
            $year = config('calendar.year');
        }

        if (intval($year) === 0) {
            abort(404);
        }

        $days = Day::with('category')->year($year)->get();

        return view('year.index', [
            'days' => $days,
            'year' => $year,
            'months' => $this->getMonths(),
            'categories' => Category::all()
        ]);
    }

    private function getMonths(): array
    {
        $months = [
            0 => ''
        ];

        foreach (range(1, 12) as $m) {
            $months[$m] = substr(date('M', mktime(0, 0, 0, $m, 1)), 0, 3);
        }

        return $months;
    }
}
