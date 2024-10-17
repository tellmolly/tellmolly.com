<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\YearService;
use Illuminate\Http\Request;

class YearController extends Controller
{
    public function index(Request $request, $year = null): array
    {
        return (new YearService)->index($request, $year);
    }
}
