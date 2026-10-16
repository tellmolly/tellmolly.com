<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GratitudeService;
use Illuminate\Http\Request;

class GratitudeController extends Controller
{
    public function index(Request $request): array
    {
        return (new GratitudeService)->index($request);
    }
}
