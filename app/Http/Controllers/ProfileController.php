<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ProfileService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', (new ProfileService)->edit($request));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            (new ProfileService)->update($request);
        } catch (\RuntimeException) {
            return Redirect::route('profile.edit');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            (new ProfileService)->destroy($request);
        } catch (\RuntimeException) {
            return Redirect::route('profile.edit');
        }

        return Redirect::to('/');
    }
}
