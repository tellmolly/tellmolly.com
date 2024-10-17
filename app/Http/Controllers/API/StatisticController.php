<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StatisticService;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index(Request $request): array
    {
        return (new StatisticService)->index($request);
    }
}
