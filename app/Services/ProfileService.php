<?php

namespace App\Services;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class ProfileService
{
    public function edit(Request $request): array
    {
        return [
            'user' => $request->user(),
        ];
    }

    public function update(ProfileUpdateRequest $request): User
    {
        if ($request->user()->email === config('calendar.demo.email')) {
            throw new \RuntimeException('Invalid email address provided');
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return $request->user();
    }

    /**
     * Signs the user out on every other browser and app: other web sessions
     * and remember-me cookies stop working, and API tokens are revoked except
     * the one making this request. The current browser stays signed in.
     */
    public function logoutOtherDevices(Request $request): void
    {
        if ($request->user()->email === config('calendar.demo.email')) {
            throw new \RuntimeException('Invalid email address provided');
        }

        $request->validateWithBag('otherDevices', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        // A fresh hash of the same password: AuthenticateSession signs out any
        // session or remember-me cookie still carrying the old one.
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logoutOtherDevices($request->input('password'));
        } else {
            $user->forceFill(['password' => Hash::make($request->input('password'))])->save();
        }

        $current = $user->currentAccessToken();
        $user->tokens()
            ->when($current instanceof PersonalAccessToken, fn($query) => $query->whereKeyNot($current->getKey()))
            ->delete();
    }

    public function destroy(Request $request): bool
    {
        if ($request->user()->email === config('calendar.demo.email')) {
            throw new \RuntimeException('Invalid email address provided');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return true;
    }
}
