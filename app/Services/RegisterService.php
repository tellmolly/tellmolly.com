<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterService
{
    public function register(Request $request): User
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tags = [
            ['name' => 'Concert', 'color' => '#007a37'],
            ['name' => 'Gym', 'color' => '#ff0000'],
            ['name' => 'Cycling', 'color' => '#2465bf'],
        ];
        foreach ($tags as $tag) {
            $user->tags()->create($tag);
        }

        event(new Registered($user));

        Auth::login($user);

        return $user;
    }
}
