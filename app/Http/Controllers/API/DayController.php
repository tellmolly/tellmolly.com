<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;
use App\Models\Day;
use App\Services\DayService;
use Illuminate\Http\Request;

class DayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Day::class, 'day');
    }

    public function index(Request $request): array
    {
        return (new DayService)->index($request);
    }

    public function create(Request $request): array
    {
        return (new DayService)->create($request);
    }

    public function store(DayStoreRequest $request): Day
    {
        return (new DayService)->store($request);
    }

    public function edit(Request $request, Day $day): array
    {
        return (new DayService)->edit($request, $day);
    }

    public function update(DayEditRequest $request, Day $day): Day
    {
        return (new DayService)->update($request, $day);
    }

    public function destroy(Day $day): Day
    {
        return (new DayService)->destroy($day);
    }

    public function jump(Request $request): array
    {
        $validated = $request->validate([
            'jump' => ['required', 'date']
        ]);

        $day = $request->user()->days()->where('date', '=', $validated['jump'])->first();

        if ( ! $day) {
            abort(404);
        }

        return $this->edit($request, $day);
    }

    public function previous(Request $request, Day $day): array
    {
        $previous = (new DayService)->previous($request, $day);

        if ( ! $previous) {
            abort(404);
        }

        return $this->edit($request, $previous);
    }

    public function next(Request $request, Day $day): array
    {
        $next = (new DayService)->next($request, $day);

        if ( ! $next) {
            abort(404);
        }

        return $this->edit($request, $next);
    }

    public function today(Request $request): array
    {
        $day = (new DayService)->today($request);

        if ( ! $day) {
            abort(404);
        }

        return $this->edit($request, $day);
    }
}
