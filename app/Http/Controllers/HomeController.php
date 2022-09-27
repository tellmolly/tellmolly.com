<?php

namespace App\Http\Controllers;

use App\Models\Day;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        return view('home.index', [
            'day' => new Day(['date' => date('Y-m-d')]),
            'categories' => $request->user()->categories,
            'tags' => $request->user()->tags
        ]);
    }
}
