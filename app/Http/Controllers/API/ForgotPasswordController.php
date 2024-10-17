<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ForgotPasswordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use RuntimeException;

class ForgotPasswordController extends Controller
{
    public function store(Request $request)
    {
        try {
            $status = (new ForgotPasswordService)->forgotPassword($request);
        } catch (RuntimeException) {
            abort(400);
        }

        return $status == Password::RESET_LINK_SENT
            ? [
                'status' => __($status)
            ]
            : [
                'errors' => ['email' => __($status)]
            ];
    }

}
