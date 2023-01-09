<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    public function login()
    {
        $demoUser = User::query()->where([
            'email' => config('calendar.demo.email')
        ])->firstOrFail();

        Auth::login($demoUser);

        return redirect()->route('days.create');
    }
}
