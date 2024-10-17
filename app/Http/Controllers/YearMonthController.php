<?php

namespace App\Http\Controllers;

use App\Services\YearMonthService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class YearMonthController extends Controller
{
    public function index(Request $request, $year = null): View
    {
        return view('year-month.index', (new YearMonthService)->index($request, $year));
    }

}
