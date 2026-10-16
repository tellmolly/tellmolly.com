<?php

namespace App\Http\Controllers;

use App\Services\ImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $result = (new ImportService)->import($request);

        if ($result['days'] === 0 && $result['tags'] === 0) {
            return Redirect::route('profile.edit')->with('import', $result['skipped']
                ? 'Nothing new to import: you already had all ' . $result['skipped'] . ' ' . Str::plural('day', $result['skipped']) . ' in this file.'
                : 'This file has no days to import.');
        }

        $message = 'Imported ' . $result['days'] . ' ' . Str::plural('day', $result['days']);
        if ($result['tags']) {
            $message .= ' and ' . $result['tags'] . ' new ' . Str::plural('tag', $result['tags']);
        }
        $message .= '.';
        if ($result['skipped']) {
            $message .= ' ' . $result['skipped'] . ' ' . Str::plural('day', $result['skipped']) . ' you already had ' . ($result['skipped'] === 1 ? 'was' : 'were') . ' left as they are.';
        }

        return Redirect::route('profile.edit')->with('import', $message);
    }
}
