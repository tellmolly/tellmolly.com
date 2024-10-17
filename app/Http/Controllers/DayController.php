<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;
use App\Models\Day;
use App\Services\DayService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Day::class, 'day');
    }

    public function index(Request $request): View
    {
        return view('day.index', (new DayService)->index($request));
    }

    public function create(Request $request): View
    {
        return view('day.create', (new DayService)->create($request));
    }

    public function store(DayStoreRequest $request): RedirectResponse
    {
        (new DayService)->store($request);

        return redirect()->route('days.index');
    }

    public function edit(Request $request, Day $day): View
    {
        return view('day.edit', (new DayService)->edit($request, $day));
    }

    public function update(DayEditRequest $request, Day $day): RedirectResponse
    {
        (new DayService)->update($request, $day);

        return redirect()->route('days.index');
    }

    public function destroy(Day $day): RedirectResponse
    {
        (new DayService)->destroy($day);

        return redirect()->route('days.index');
    }

    public function jump(Request $request): RedirectResponse
    {
        $day = (new DayService)->jump($request);

        if ( ! $day) {
            return redirect()->route('days.index')->with([
                'message' => 'No entry on this day'
            ]);
        }

        return redirect()->route('days.edit', $day);
    }

    public function previous(Request $request, Day $day): RedirectResponse
    {
        $previous = (new DayService)->previous($request, $day);

        if ( ! $previous) {
            return redirect()->route('days.edit', $day)->with([
                'message' => 'No earlier entry available'
            ]);
        }

        return redirect()->route('days.edit', $previous);
    }

    public function next(Request $request, Day $day): RedirectResponse
    {
        $next = (new DayService)->next($request, $day);

        if ( ! $next) {
            return redirect()->route('days.edit', $day)->with([
                'message' => 'No later entry available'
            ]);
        }

        return redirect()->route('days.edit', $next);
    }
}
