<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\LoginService;

class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        $user = (new LoginService)->login($request);

        return [
            'name' => $user->name
        ];
    }
}
