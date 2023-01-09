<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function privacyPolicy(): View
    {
        return view('page.privacy_policy');
    }

    public function demo(): View
    {
        return view('page.demo');
    }

    public function faq(): View
    {
        return view('page.faq');
    }
}
