<?php

namespace App\Http\Controllers;

use App\Day;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

class YearController extends Controller
{
    /**
     * @param Request $request
     * @param null    $year
     *
     * @return Renderable
     */
    public function index(Request $request, $year = null)
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

    private function getMonths()
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
