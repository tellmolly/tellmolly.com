<?php

namespace App\Services;

use Illuminate\Http\Request;

class GratitudeService
{
    public function index(Request $request): array
    {
        return [
            'days' => $request->user()->days()->with('category')
                ->whereNotNull('grateful_for')
                ->where('grateful_for', '<>', '')
                ->orderByDesc('date')
                ->paginate(30),
        ];
    }
}
