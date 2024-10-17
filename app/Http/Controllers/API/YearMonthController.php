<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\YearMonthService;
use Illuminate\Http\Request;

class YearMonthController extends Controller
{
    public function index(Request $request, $year = null): array
    {
        return (new YearMonthService)->index($request, $year);
    }

}
