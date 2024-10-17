<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\YearInReviewService;
use Illuminate\Http\Request;

class YearInReviewController extends Controller
{
    public function show(Request $request): array
    {
        return (new YearInReviewService)->get($request);
    }
}
