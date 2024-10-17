<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordService
{
    public function forgotPassword(Request $request): bool
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        if ($request->input('email') === config('calendar.demo.email')) {
            throw new \RuntimeException('Invalid email address provided');
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        return Password::sendResetLink(
            $request->only('email')
        );
    }
}
