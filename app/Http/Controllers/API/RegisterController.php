<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\RegisterService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $user = (new RegisterService)->register($request);

        return [
            'name' => $user->name,
            'token' => $user->createToken('App')->plainTextToken
        ];
    }

}
