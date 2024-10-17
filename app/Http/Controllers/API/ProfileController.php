<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use RuntimeException;

class ProfileController extends Controller
{
    public function edit(Request $request): array
    {
        return (new ProfileService)->edit($request);
    }

    public function update(ProfileUpdateRequest $request): array
    {
        try {
            (new ProfileService)->update($request);
        } catch (RuntimeException) {
            abort(400);
        }

        return ['status' => 'profile-updated'];
    }

    public function destroy(Request $request): array
    {
        try {
            (new ProfileService)->destroy($request);
        } catch (RuntimeException) {
            abort(400);
        }

        return [
            'status' => 'success'
        ];
    }
}
