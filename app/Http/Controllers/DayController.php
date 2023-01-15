<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayEditRequest;
use App\Http\Requests\DayStoreRequest;
use App\Models\Category;
use App\Models\Day;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DayController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Day::class, 'day');
    }

    public function index(Request $request): View
    {
        return view('day.index', [
            'isSearch' => $request->get('search') !== null,
            'days' => $request->user()->days()->with('category', 'tags')->search($request->get('search'))->orderByDesc('date')->paginate(30)->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        $validated = $request->validate([
            'initial' => ['sometimes', 'date_format:Y-m-d']
        ]);

        return view('day.create', [
            'day' => new Day(['date' => $validated['initial'] ?? date('Y-m-d')]),
            'categories' => Category::orderBy('order')->get(),
            'tags' => $request->user()->tags
        ]);
    }

    public function store(DayStoreRequest $request): RedirectResponse
    {
        $day = new Day();
        $day->fill($request->validated());

        $request->user()->days()->save($day);

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    public function edit(Request $request, Day $day): View
    {
        return view('day.edit', [
            'day' => $day,
            'categories' => Category::orderBy('order')->get(),
            'tags' => $request->user()->tags
        ]);
    }

    public function update(DayEditRequest $request, Day $day): RedirectResponse
    {
        $day->fill($request->validated());
        $day->save();

        $day->tags()->sync($request->tag_ids);

        return redirect()->route('days.index');
    }

    public function destroy(Day $day): RedirectResponse
    {
        $day->delete();

        return redirect()->route('days.index');
    }

    public function jump(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jump' => ['required', 'date']
        ]);

        $day = $request->user()->days()->where('date', '=', $validated['jump'])->first();

        if ( ! $day) {
            return redirect()->route('days.index')->with([
                'message' => 'No entry on this day'
            ]);
        }

        return redirect()->route('days.edit', $day);
    }

    public function previous(Request $request, Day $day): RedirectResponse
    {
        $previous = $request->user()->days()
            ->where('date', '<', $day->date)
            ->orderByDesc('date')
            ->first();

        if ( ! $previous) {
            return redirect()->route('days.edit', $day)->with([
                'message' => 'No earlier entry available'
            ]);
        }

        return redirect()->route('days.edit', $previous);
    }

    public function next(Request $request, Day $day): RedirectResponse
    {
        $next = $request->user()->days()
            ->where('date', '>', $day->date)
            ->orderBy('date', 'asc')
            ->first();

        if ( ! $next) {
            return redirect()->route('days.edit', $day)->with([
                'message' => 'No later entry available'
            ]);
        }

        return redirect()->route('days.edit', $next);
    }
}
