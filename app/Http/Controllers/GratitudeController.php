<?php

namespace App\Http\Controllers;

use App\Services\GratitudeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GratitudeController extends Controller
{
    public function index(Request $request): View
    {
        return view('gratitude.index', (new GratitudeService)->index($request));
    }
}
