<?php

namespace App\Http\Controllers;

use App\Services\StatisticService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index(Request $request): View
    {
        return view('statistic.index', (new StatisticService)->index($request));
    }
}
