<?php

namespace App\Services;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
