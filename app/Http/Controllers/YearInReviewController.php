<?php

namespace App\Http\Controllers;

use App\Services\YearInReviewService;
use Illuminate\Http\Request;

class YearInReviewController extends Controller
{
    public function show(Request $request)
    {
        return view('year-in-review.show', (new YearInReviewService)->get($request));
    }
}
