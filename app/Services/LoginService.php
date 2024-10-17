<?php

namespace App\Services;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;

class LoginService
{

    public function login(LoginRequest $request): User
    {
        $request->authenticate();

        return $request->user();
    }
}
