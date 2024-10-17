<?php

namespace App\Http\Controllers;

use App\Services\YearService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function index(Request $request, $year = null): View
    {
        return view('year.index', (new YearService)->index($request, $year));
    }
}
