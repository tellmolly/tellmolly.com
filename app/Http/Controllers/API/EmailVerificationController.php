<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function store(Request $request): array
    {
        if ($request->user()->hasVerifiedEmail()) {
            return ['status' => 'already-verified'];
        }

        $request->user()->sendEmailVerificationNotification();

        return ['status' => 'verification-link-sent'];
    }
}
